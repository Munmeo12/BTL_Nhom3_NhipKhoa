<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class ImpactBox
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function forUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT ib.id, ib.user_id, ib.post_id, ib.note, ib.created_at,
                    p.title, p.slug, p.thumbnail, p.category_id, c.name AS category_name
             FROM impact_box_items ib
             INNER JOIN posts p ON p.id = ib.post_id
             INNER JOIN categories c ON c.id = p.category_id
             WHERE ib.user_id = ? AND p.status = 'published' AND c.status = 'active'
             ORDER BY ib.created_at DESC"
        );
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }

    public function find(int $userId, int $postId): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT ib.id, ib.user_id, ib.post_id, ib.note, ib.created_at,
                    p.title, p.slug, p.thumbnail, p.category_id, c.name AS category_name
             FROM impact_box_items ib
             INNER JOIN posts p ON p.id = ib.post_id
             INNER JOIN categories c ON c.id = p.category_id
             WHERE ib.user_id = ? AND ib.post_id = ?
               AND p.status = 'published' AND c.status = 'active'
             LIMIT 1"
        );
        $statement->execute([$userId, $postId]);
        return $statement->fetch() ?: null;
    }

    public function exists(int $userId, int $postId): bool
    {
        return $this->find($userId, $postId) !== null;
    }

    public function canSavePost(int $postId): bool
    {
        $statement = $this->pdo->prepare(
            "SELECT p.id FROM posts p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.id = ? AND p.status = 'published' AND c.status = 'active' LIMIT 1"
        );
        $statement->execute([$postId]);
        return $statement->fetchColumn() !== false;
    }

    public function add(int $userId, int $postId, ?string $note): bool
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO impact_box_items (user_id, post_id, note) VALUES (?, ?, ?)'
        );
        return $statement->execute([$userId, $postId, $note]);
    }

    public function delete(int $userId, int $postId): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM impact_box_items WHERE user_id = ? AND post_id = ?');
        return $statement->execute([$userId, $postId]);
    }

    public function updateNote(int $userId, int $postId, ?string $note): bool
    {
        $statement = $this->pdo->prepare('UPDATE impact_box_items SET note = ? WHERE user_id = ? AND post_id = ?');
        return $statement->execute([$note, $userId, $postId]);
    }
}
