<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class User
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT u.id, u.email, u.password_hash, u.full_name, u.status, r.code AS role
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        return $statement->fetch() ?: null;
    }

    public function createReader(string $fullName, string $email, string $passwordHash): bool
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO users (role_id, email, password_hash, full_name)
             SELECT id, :email, :password_hash, :full_name
             FROM roles
             WHERE code = 'reader'
             LIMIT 1"
        );
        $statement->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'full_name' => $fullName,
        ]);
        return $statement->rowCount() === 1;
    }

    public function status(int $id): ?string
    {
        $statement = $this->pdo->prepare('SELECT status FROM users WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        $status = $statement->fetchColumn();
        return is_string($status) ? $status : null;
    }

    public function adminDashboard(): array
    {
        $counts = [
            'users' => (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'posts' => (int) $this->pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn(),
            'pending' => (int) $this->pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'pending'")->fetchColumn(),
            'locked' => (int) $this->pdo->query("SELECT COUNT(*) FROM users WHERE status = 'locked'")->fetchColumn(),
        ];
        $recent = $this->pdo->query(
            'SELECT u.full_name, u.email, u.status, u.created_at, r.name AS role_name
             FROM users u INNER JOIN roles r ON r.id = u.role_id
             ORDER BY u.created_at DESC LIMIT 5'
        )->fetchAll();
        return ['counts' => $counts, 'recent' => $recent];
    }

    public function search(string $keyword, string $role, string $status): array
    {
        $conditions = [];
        $parameters = [];
        if ($keyword !== '') {
            // Prepare thật (EMULATE_PREPARES=false) không cho dùng lại một
            // placeholder, nên mỗi cột cần một tên riêng.
            $conditions[] = '(u.full_name LIKE :full_name OR u.email LIKE :email)';
            $value = '%' . $keyword . '%';
            $parameters += ['full_name' => $value, 'email' => $value];
        }
        if ($role !== '') {
            $conditions[] = 'r.code = :role';
            $parameters['role'] = $role;
        }
        if ($status !== '') {
            $conditions[] = 'u.status = :status';
            $parameters['status'] = $status;
        }
        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $statement = $this->pdo->prepare(
            'SELECT u.id, u.full_name, u.email, u.status, u.created_at,
                    r.code AS role, r.name AS role_name
             FROM users u INNER JOIN roles r ON r.id = u.role_id' . $where .
            ' ORDER BY u.created_at DESC LIMIT 50'
        );
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function adminTarget(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT u.status, r.code AS role FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE u.id = ?'
        );
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function changeRole(int $id, string $role): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET role_id = (SELECT id FROM roles WHERE code = ? LIMIT 1) WHERE id = ?'
        );
        return $statement->execute([$role, $id]);
    }

    public function changeStatus(int $id, string $status): bool
    {
        $statement = $this->pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
        return $statement->execute([$status, $id]);
    }
}
