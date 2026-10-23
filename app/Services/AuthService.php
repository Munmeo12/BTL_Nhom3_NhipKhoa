<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Response;

final class AuthService
{
    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function loggedIn(): bool
    {
        return $this->user() !== null;
    }

    public function authenticate(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'email' => (string) $user['email'],
            'full_name' => (string) $user['full_name'],
            'role' => (string) $user['role'],
        ];
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public function landingPage(string $role): string
    {
        return match ($role) {
            'admin' => 'admin/dashboard.php',
            'editor' => 'editor/dashboard.php',
            'author' => 'author/dashboard.php',
            default => 'index.php',
        };
    }

    public function requireRole(array $roles): array
    {
        $user = $this->user();
        if ($user === null) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập để tiếp tục.';
            Response::redirect(BASE_URL . 'dang-nhap.php');
        }
        if (!in_array((string) ($user['role'] ?? 'reader'), $roles, true)) {
            Response::abort(403, '403 - Bạn không có quyền truy cập chức năng này.');
        }
        return $user;
    }
}
