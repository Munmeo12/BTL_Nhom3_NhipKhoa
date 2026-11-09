<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

/** Thông báo một lần gửi tới người dùng, hiện ở lần tải trang kế tiếp. */
final class UserNotification
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function add(int $userId, string $message): void
    {
        $statement = $this->pdo->prepare('INSERT INTO user_notifications (user_id, message) VALUES (?, ?)');
        $statement->execute([$userId, mb_substr($message, 0, 600)]);
    }

    /** Lấy các thông báo chưa đọc rồi đánh dấu đã đọc. @return string[] */
    public function pullUnread(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, message FROM user_notifications
             WHERE user_id = ? AND read_at IS NULL
             ORDER BY id ASC LIMIT 10'
        );
        $statement->execute([$userId]);
        $rows = $statement->fetchAll();
        if ($rows === []) {
            return [];
        }
        $lastId = (int) end($rows)['id'];
        $this->pdo->prepare(
            'UPDATE user_notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL AND id <= ?'
        )->execute([$userId, $lastId]);
        return array_map(static fn(array $row): string => (string) $row['message'], $rows);
    }
}
