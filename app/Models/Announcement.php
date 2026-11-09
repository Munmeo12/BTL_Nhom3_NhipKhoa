<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Announcement
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** Danh sách nội dung thông báo đang bật (mới nhất trước) cho thanh chạy ở đầu trang. */
    public function activeTexts(): array
    {
        return $this->pdo->query(
            'SELECT content FROM announcements WHERE is_active = 1 ORDER BY id DESC'
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT id, content, is_active, created_at FROM announcements ORDER BY id DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT id, content, is_active FROM announcements WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function create(string $content): bool
    {
        return $this->pdo->prepare('INSERT INTO announcements (content, is_active) VALUES (?, 1)')->execute([$content]);
    }

    public function update(int $id, string $content): bool
    {
        return $this->pdo->prepare('UPDATE announcements SET content = ? WHERE id = ?')->execute([$content, $id]);
    }

    public function toggle(int $id): bool
    {
        return $this->pdo->prepare('UPDATE announcements SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
    }

    public function delete(int $id): bool
    {
        return $this->pdo->prepare('DELETE FROM announcements WHERE id = ?')->execute([$id]);
    }
}
