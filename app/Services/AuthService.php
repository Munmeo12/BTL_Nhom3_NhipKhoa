<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Response;

final class AuthService
{
    /** id người dùng đã được kiểm tra lại với CSDL trong request hiện tại. */
    private static ?int $verifiedId = null;

    public function user(): ?array
    {
        $user = $_SESSION['user'] ?? null;
        if ($user === null) {
            return null;
        }

        $id = (int) ($user['id'] ?? 0);
        if (self::$verifiedId === $id) {
            return $user;
        }

        // Session chỉ lưu thông tin lúc đăng nhập. Nếu Admin khóa tài khoản hoặc đổi
        // vai trò sau đó, phải đối chiếu lại CSDL để quyết định có hiệu lực ngay.
        try {
            $state = (new \App\Models\User(db()))->sessionState($id);
        } catch (\Throwable) {
            return $user; // Mất kết nối CSDL: giữ phiên hiện tại, trang sẽ tự báo lỗi dữ liệu.
        }

        if ($state === null || $state['status'] !== 'active') {
            unset($_SESSION['user']);
            $_SESSION['flash_error'] = 'Tài khoản của bạn đã bị khóa hoặc không còn tồn tại.';
            return null;
        }

        $_SESSION['user']['role'] = (string) $state['role'];
        $_SESSION['user']['full_name'] = (string) $state['full_name'];
        self::$verifiedId = $id;
        return $_SESSION['user'];
    }

    public function loggedIn(): bool
    {
        return $this->user() !== null;
    }

    public function authenticate(array $user): void
    {
        session_regenerate_id(true);
        self::$verifiedId = (int) $user['id'];
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
        self::$verifiedId = null;
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
