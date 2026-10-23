<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';

function currentUser(): ?array
{
    return (new App\Services\AuthService())->user();
}

function roleLandingPage(string $role): string
{
    return (new App\Services\AuthService())->landingPage($role);
}

function requireLogin(): void
{
    if (currentUser() === null) {
        $_SESSION['flash_error'] = 'Vui lòng đăng nhập để tiếp tục.';
        redirect(BASE_URL . 'dang-nhap.php');
    }
}

function requireRole(array $allowedRoles): void
{
    requireLogin();
    $role = currentUser()['role'] ?? 'reader';
    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        exit('403 - Bạn không có quyền truy cập chức năng này.');
    }
}
