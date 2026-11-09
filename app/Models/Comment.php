<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Comment
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function approvedForPost(int $postId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT comments.id, comments.content, comments.created_at, comments.user_id,
                    users.full_name, users.avatar, roles.code AS role
             FROM comments
             INNER JOIN users ON users.id = comments.user_id
             INNER JOIN roles ON roles.id = users.role_id
             WHERE comments.post_id = ? AND comments.status = 'approved'
             ORDER BY comments.created_at DESC, comments.id DESC"
        );
        $statement->execute([$postId]);
        return $statement->fetchAll();
    }

    public function createPending(int $postId, int $userId, string $content): bool
    {
        return $this->create($postId, $userId, $content, 'pending');
    }

    /** Quy tắc: bình luận của Admin hiển thị ngay; vai trò khác phải chờ Admin duyệt. */
    public function createForRole(int $postId, int $userId, string $content, string $role): string
    {
        $status = $role === 'admin' ? 'approved' : 'pending';
        $this->create($postId, $userId, $content, $status);
        return $status;
    }

    private function create(int $postId, int $userId, string $content, string $status): bool
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO comments (post_id, user_id, content, status, created_at)
             VALUES (?, ?, ?, ?, NOW())"
        );
        return $statement->execute([$postId, $userId, $content, $status]);
    }

    public function search(string $keyword = '', string $status = 'all', int $postId = 0): array
    {
        $sql = 'SELECT c.id, c.content, c.status, c.created_at,
                       u.full_name AS user_name, u.avatar AS user_avatar, r.code AS user_role, p.title AS post_title
                FROM comments c LEFT JOIN users u ON u.id = c.user_id
                LEFT JOIN roles r ON r.id = u.role_id
                LEFT JOIN posts p ON p.id = c.post_id WHERE 1=1';
        $parameters = [];
        if ($keyword !== '') {
            $sql .= ' AND (c.content LIKE :content OR u.full_name LIKE :user OR p.title LIKE :post)';
            $value = '%' . $keyword . '%';
            $parameters += ['content' => $value, 'user' => $value, 'post' => $value];
        }
        if (in_array($status, ['pending', 'approved', 'hidden'], true)) {
            $sql .= ' AND c.status = :status';
            $parameters['status'] = $status;
        }
        if ($postId > 0) {
            $sql .= ' AND c.post_id = :post_id';
            $parameters['post_id'] = $postId;
        }
        $statement = $this->pdo->prepare($sql . ' ORDER BY c.created_at DESC');
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function posts(): array
    {
        return $this->pdo->query('SELECT id, title FROM posts ORDER BY title ASC')->fetchAll();
    }

    public function findDetailed(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT c.id, c.content, c.status, c.created_at AS comment_created_at,
                    u.id AS user_id, u.full_name AS user_name, u.avatar AS user_avatar, u.email AS user_email,
                    p.id AS post_id, p.title AS post_title, p.thumbnail AS post_thumbnail,
                    p.created_at AS post_created_at
             FROM comments c LEFT JOIN users u ON u.id = c.user_id
             LEFT JOIN posts p ON p.id = c.post_id WHERE c.id = ?'
        );
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $statement = $this->pdo->prepare('UPDATE comments SET status = ? WHERE id = ?');
        return $statement->execute([$status, $id]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM comments WHERE id = ?');
        return $statement->execute([$id]);
    }
}
