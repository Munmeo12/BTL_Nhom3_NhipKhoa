<?php 
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Tag
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT t.id, t.slug, t.name, t.created_at, COUNT(pt.post_id) AS post_count
             FROM tags t LEFT JOIN post_tags pt ON pt.tag_id = t.id
             GROUP BY t.id, t.slug, t.name, t.created_at
             ORDER BY t.name ASC'
        )->fetchAll();
    }

    public function forSelect(): array
    {
        return $this->pdo->query('SELECT id, name FROM tags ORDER BY name ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, slug, name FROM tags WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, slug, name FROM tags WHERE slug = ? LIMIT 1');
        $statement->execute([$slug]);
        return $statement->fetch() ?: null;
    }

    public function exists(string $name, string $slug, int $exceptId = 0): bool
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM tags WHERE (name = ? OR slug = ?) AND id <> ?');
        $statement->execute([$name, $slug, $exceptId]);
        return (int) $statement->fetchColumn() > 0;
    }

    public function create(string $name, string $slug): bool
    {
        $statement = $this->pdo->prepare('INSERT INTO tags (name, slug) VALUES (?, ?)');
        return $statement->execute([$name, $slug]);
    }

    public function update(int $id, string $name, string $slug): bool
    {
        $statement = $this->pdo->prepare('UPDATE tags SET name = ?, slug = ? WHERE id = ?');
        return $statement->execute([$name, $slug, $id]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM tags WHERE id = ?');
        return $statement->execute([$id]);
    }

    public function forPost(int $postId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT t.id, t.slug, t.name FROM tags t
             INNER JOIN post_tags pt ON pt.tag_id = t.id
             WHERE pt.post_id = ? ORDER BY t.name ASC'
        );
        $statement->execute([$postId]);
        return $statement->fetchAll();
    }

    /** @return int[] */
    public function idsForPost(int $postId): array
    {
        return array_map('intval', array_column($this->forPost($postId), 'id'));
    }

    /**
     * Thay toàn bộ thẻ của bài viết bằng danh sách mới. Id không tồn tại bị bỏ qua.
     *
     * @param int[] $tagIds
     */
    public function sync(int $postId, array $tagIds): void
    {
        $own = !$this->pdo->inTransaction();
        if ($own) {
            $this->pdo->beginTransaction();
        }
        try {
            $this->pdo->prepare('DELETE FROM post_tags WHERE post_id = ?')->execute([$postId]);
            $insert = $this->pdo->prepare(
                'INSERT IGNORE INTO post_tags (post_id, tag_id) SELECT ?, id FROM tags WHERE id = ?'
            );
            foreach ($tagIds as $tagId) {
                $insert->execute([$postId, (int) $tagId]);
            }
            if ($own) {
                $this->pdo->commit();
            }
        } catch (\Throwable $exception) {
            if ($own && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
