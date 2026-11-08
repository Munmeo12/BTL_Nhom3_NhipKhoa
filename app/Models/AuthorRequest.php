<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Yêu cầu nâng quyền Độc giả -> Tác giả.
 * Mọi truy vấn dùng prepared statement; việc duyệt chạy trong transaction để
 * hai Admin bấm cùng lúc cũng không xử lý một yêu cầu hai lần.
 */
final class AuthorRequest
{
    public const STATUSES = ['pending', 'approved', 'rejected'];

    public function __construct(private readonly PDO $pdo)
    {
    }

    /** Yêu cầu mới nhất của một người (để hiện trạng thái cho chính họ). */
    public function latestForUser(int $userId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, contact, topics, reason, sample_link, status, admin_note, reviewed_at, created_at
             FROM author_requests
             WHERE user_id = ?
             ORDER BY id DESC
             LIMIT 1'
        );
        $statement->execute([$userId]);
        return $statement->fetch() ?: null;
    }

    /** Toàn bộ lịch sử yêu cầu của một người, mới nhất trước. */
    public function historyForUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, status, topics, admin_note, reviewed_at, created_at
             FROM author_requests
             WHERE user_id = ?
             ORDER BY id DESC
             LIMIT 10'
        );
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }

    /** Trả false nếu người này đã có một yêu cầu đang chờ. */
    public function create(int $userId, string $contact, string $topics, string $reason, string $sampleLink): bool
    {
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO author_requests (user_id, contact, topics, reason, sample_link)
                 VALUES (:user_id, :contact, :topics, :reason, :sample_link)'
            );
            $statement->execute([
                'user_id' => $userId,
                'contact' => $contact !== '' ? $contact : null,
                'topics' => $topics,
                'reason' => $reason,
                'sample_link' => $sampleLink !== '' ? $sampleLink : null,
            ]);
            return true;
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') { // trùng UNIQUE(user_id, pending_flag)
                return false;
            }
            throw $exception;
        }
    }

    public function counts(): array
    {
        $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        $rows = $this->pdo->query('SELECT status, COUNT(*) AS total FROM author_requests GROUP BY status')->fetchAll();
        foreach ($rows as $row) {
            $counts[(string) $row['status']] = (int) $row['total'];
        }
        return $counts;
    }

    public function listForAdmin(string $status): array
    {
        $where = '';
        $parameters = [];
        if (in_array($status, self::STATUSES, true)) {
            $where = ' WHERE r.status = :status';
            $parameters['status'] = $status;
        }
        $statement = $this->pdo->prepare(
            'SELECT r.id, r.user_id, r.contact, r.topics, r.reason, r.sample_link, r.status,
                    r.admin_note, r.reviewed_at, r.created_at,
                    u.full_name, u.avatar, u.email, u.status AS user_status, ro.code AS role,
                    rv.full_name AS reviewer_name
             FROM author_requests r
             INNER JOIN users u ON u.id = r.user_id
             INNER JOIN roles ro ON ro.id = u.role_id
             LEFT JOIN users rv ON rv.id = r.reviewed_by' . $where . '
             ORDER BY (r.status = \'pending\') DESC, r.created_at DESC
             LIMIT 100'
        );
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    /**
     * Duyệt: đổi vai trò Độc giả -> Tác giả và đóng yêu cầu trong cùng một transaction.
     * Chỉ nâng khi người đó đang là Độc giả (không hạ quyền Biên tập viên/Admin).
     */
    public function approve(int $requestId, int $adminId): string
    {
        $this->pdo->beginTransaction();
        try {
            $request = $this->lockPending($requestId);
            $statement = $this->pdo->prepare(
                'SELECT u.status, ro.code AS role
                 FROM users u INNER JOIN roles ro ON ro.id = u.role_id
                 WHERE u.id = ?'
            );
            $statement->execute([$request['user_id']]);
            $user = $statement->fetch();
            if (!$user) {
                throw new RuntimeException('Tài khoản của yêu cầu này không còn tồn tại.');
            }
            if ($user['status'] !== 'active') {
                throw new RuntimeException('Tài khoản đang bị khóa, hãy mở khóa trước khi duyệt.');
            }

            $promoted = false;
            if ($user['role'] === 'reader') {
                $this->pdo->prepare(
                    "UPDATE users SET role_id = (SELECT id FROM roles WHERE code = 'author' LIMIT 1) WHERE id = ?"
                )->execute([$request['user_id']]);
                $promoted = true;
            }

            $this->close($requestId, 'approved', $adminId, null);
            $this->pdo->commit();
            return $promoted ? 'promoted' : 'already';
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function userIdOf(int $requestId): ?int
    {
        $statement = $this->pdo->prepare('SELECT user_id FROM author_requests WHERE id = ?');
        $statement->execute([$requestId]);
        $id = $statement->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    public function reject(int $requestId, int $adminId, string $note): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->lockPending($requestId);
            $this->close($requestId, 'rejected', $adminId, $note);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    private function lockPending(int $requestId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT id, user_id FROM author_requests WHERE id = ? AND status = 'pending' FOR UPDATE"
        );
        $statement->execute([$requestId]);
        $request = $statement->fetch();
        if (!$request) {
            throw new RuntimeException('Yêu cầu không tồn tại hoặc đã được xử lý.');
        }
        return $request;
    }

    private function close(int $requestId, string $status, int $adminId, ?string $note): void
    {
        $this->pdo->prepare(
            'UPDATE author_requests
             SET status = ?, reviewed_by = ?, reviewed_at = NOW(), admin_note = ?
             WHERE id = ?'
        )->execute([$status, $adminId, $note, $requestId]);
    }
}
