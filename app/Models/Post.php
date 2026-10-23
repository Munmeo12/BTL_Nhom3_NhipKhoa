<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Post
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function publishedForHome(int $limit = 4): array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.id, p.title, p.summary, p.thumbnail, p.published_at, p.created_at,
                    c.name AS category_name, c.slug AS category_slug, u.full_name AS author_name
             FROM posts p
             INNER JOIN categories c ON c.id = p.category_id
             INNER JOIN users u ON u.id = p.author_id
             WHERE p.status = 'published'
               AND c.status = 'active'
               AND c.slug IN ('tin-khoa', 'hoc-tap', 'co-hoi', 'su-kien')
             ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
             LIMIT ?"
        );
        $statement->bindValue(1, max(1, $limit), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function publishedList(?string $category, string $keyword, int $page, int $perPage = 6): array
    {
        $where = "p.status = 'published' AND c.status = 'active'";
        $arguments = [];
        if ($category !== null) {
            $where .= ' AND c.slug = ?';
            $arguments[] = $category;
        }
        if ($keyword !== '') {
            $where .= ' AND (p.title LIKE ? OR p.summary LIKE ? OR p.content LIKE ? OR c.name LIKE ? OR u.full_name LIKE ?)';
            array_push($arguments, ...array_fill(0, 5, '%' . $keyword . '%'));
        }

        $joins = ' FROM posts p INNER JOIN categories c ON c.id = p.category_id INNER JOIN users u ON u.id = p.author_id ';
        $count = $this->pdo->prepare('SELECT COUNT(*)' . $joins . 'WHERE ' . $where);
        $count->execute($arguments);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));

        $statement = $this->pdo->prepare(
            'SELECT p.id, p.title, p.summary, p.thumbnail, p.published_at, p.created_at,
                    c.name AS category_name, c.slug AS category_slug, u.full_name AS author_name' .
            $joins . 'WHERE ' . $where .
            ' ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC LIMIT ? OFFSET ?'
        );
        foreach ($arguments as $index => $argument) {
            $statement->bindValue($index + 1, $argument, PDO::PARAM_STR);
        }
        $statement->bindValue(count($arguments) + 1, $perPage, PDO::PARAM_INT);
        $statement->bindValue(count($arguments) + 2, ($page - 1) * $perPage, PDO::PARAM_INT);
        $statement->execute();

        return [
            'posts' => $statement->fetchAll(),
            'total' => $total,
            'pages' => $pages,
            'page' => $page,
        ];
    }

    public function findPublished(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.full_name AS author_name
             FROM posts p
             INNER JOIN categories c ON c.id = p.category_id
             INNER JOIN users u ON u.id = p.author_id
             WHERE p.id = ? AND p.status = 'published' AND c.status = 'active'
             LIMIT 1"
        );
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function relatedPublished(int $categoryId, int $exceptId, int $limit = 3): array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.id, p.title, p.thumbnail, p.published_at, p.created_at
             FROM posts p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.category_id = ? AND p.id <> ?
               AND p.status = 'published' AND c.status = 'active'
             ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
             LIMIT ?"
        );
        $statement->bindValue(1, $categoryId, PDO::PARAM_INT);
        $statement->bindValue(2, $exceptId, PDO::PARAM_INT);
        $statement->bindValue(3, max(1, $limit), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function authorCounts(int $authorId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) AS total,
                    SUM(status = 'draft') AS draft,
                    SUM(status = 'pending') AS pending,
                    SUM(status = 'published') AS published,
                    SUM(status = 'rejected') AS rejected
             FROM posts WHERE author_id = ?"
        );
        $statement->execute([$authorId]);
        $row = $statement->fetch() ?: [];
        return array_map('intval', [
            'total' => $row['total'] ?? 0,
            'draft' => $row['draft'] ?? 0,
            'pending' => $row['pending'] ?? 0,
            'published' => $row['published'] ?? 0,
            'rejected' => $row['rejected'] ?? 0,
        ]);
    }

    public function byAuthor(int $authorId, ?string $status = null, ?int $limit = null): array
    {
        $sql = 'SELECT p.id, p.title, p.status, p.updated_at, c.name AS category_name
                FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.author_id = ?';
        $arguments = [$authorId];
        if ($status !== null) {
            $sql .= ' AND p.status = ?';
            $arguments[] = $status;
        }
        $sql .= ' ORDER BY p.updated_at DESC';
        if ($limit !== null) {
            $sql .= ' LIMIT ?';
        }
        $statement = $this->pdo->prepare($sql);
        foreach ($arguments as $index => $argument) {
            $statement->bindValue($index + 1, $argument, is_int($argument) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        if ($limit !== null) {
            $statement->bindValue(count($arguments) + 1, $limit, PDO::PARAM_INT);
        }
        $statement->execute();
        return $statement->fetchAll();
    }

    public function findOwned(int $postId, int $authorId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.*, c.name AS category_name, u.full_name AS author_name
             FROM posts p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN users u ON u.id = p.author_id
             WHERE p.id = ? AND p.author_id = ? LIMIT 1'
        );
        $statement->execute([$postId, $authorId]);
        return $statement->fetch() ?: null;
    }

    public function create(array $values): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO posts (author_id, category_id, title, slug, summary, thumbnail, content, status)
             VALUES (:author_id, :category_id, :title, :slug, :summary, :thumbnail, :content, :status)'
        );
        $statement->execute($values);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateOwned(int $postId, int $authorId, array $values, bool $submit): bool
    {
        $reviewReset = $submit
            ? ', reviewer_id = NULL, editor_note = NULL, published_at = NULL'
            : '';
        $statement = $this->pdo->prepare(
            'UPDATE posts SET category_id = :category_id, title = :title, summary = :summary,
             thumbnail = :thumbnail, content = :content, status = :status' . $reviewReset .
            ' WHERE id = :id AND author_id = :author_id'
        );
        return $statement->execute($values + ['id' => $postId, 'author_id' => $authorId]);
    }

    public function deleteOwned(int $postId, int $authorId): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM posts WHERE id = ? AND author_id = ?');
        $statement->execute([$postId, $authorId]);
        return $statement->rowCount() === 1;
    }

    public function editorCounts(): array
    {
        $row = $this->pdo->query(
            "SELECT SUM(status IN ('pending','published','rejected')) AS total,
                    SUM(status = 'pending') AS pending,
                    SUM(status = 'published') AS published,
                    SUM(status = 'rejected') AS rejected FROM posts"
        )->fetch() ?: [];
        return array_map('intval', [
            'total' => $row['total'] ?? 0,
            'pending' => $row['pending'] ?? 0,
            'published' => $row['published'] ?? 0,
            'rejected' => $row['rejected'] ?? 0,
        ]);
    }

    public function pendingForEditor(int $limit = 3): array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.id, p.title, p.thumbnail, p.created_at,
                    u.full_name AS author_name, c.name AS category_name
             FROM posts p
             INNER JOIN users u ON u.id = p.author_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status = 'pending' ORDER BY p.created_at DESC LIMIT ?"
        );
        $statement->bindValue(1, $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function forEditor(string $status, int $page, int $perPage = 5): array
    {
        $where = "p.status IN ('pending','published','rejected')";
        $arguments = [];
        if ($status !== 'all') {
            $where .= ' AND p.status = ?';
            $arguments[] = $status;
        }
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM posts p WHERE ' . $where);
        $count->execute($arguments);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $statement = $this->pdo->prepare(
            'SELECT p.id, p.title, p.thumbnail, p.status, p.created_at,
                    u.full_name AS author_name, c.name AS category_name
             FROM posts p INNER JOIN users u ON u.id = p.author_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE ' . $where . ' ORDER BY p.created_at DESC LIMIT ? OFFSET ?'
        );
        $position = 1;
        foreach ($arguments as $argument) {
            $statement->bindValue($position++, $argument, PDO::PARAM_STR);
        }
        $statement->bindValue($position++, $perPage, PDO::PARAM_INT);
        $statement->bindValue($position, ($page - 1) * $perPage, PDO::PARAM_INT);
        $statement->execute();
        return ['posts' => $statement->fetchAll(), 'page' => $page, 'pages' => $pages, 'total' => $total];
    }

    public function findForReview(int $postId): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.*, u.full_name AS author_name, c.name AS category_name
             FROM posts p INNER JOIN users u ON u.id = p.author_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = ? AND p.status IN ('pending','published','rejected') LIMIT 1"
        );
        $statement->execute([$postId]);
        return $statement->fetch() ?: null;
    }

    public function review(int $postId, int $editorId, string $action, ?string $note = null): bool
    {
        if ($action === 'approve') {
            $statement = $this->pdo->prepare(
                "UPDATE posts SET status = 'published', reviewer_id = ?, editor_note = NULL,
                 published_at = NOW() WHERE id = ? AND status = 'pending'"
            );
            $statement->execute([$editorId, $postId]);
        } else {
            $statement = $this->pdo->prepare(
                "UPDATE posts SET status = 'rejected', reviewer_id = ?, editor_note = ?,
                 published_at = NULL WHERE id = ? AND status = 'pending'"
            );
            $statement->execute([$editorId, $note, $postId]);
        }
        return $statement->rowCount() === 1;
    }
}
