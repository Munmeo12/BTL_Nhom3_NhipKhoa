<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nhip_khoa_sessions';

    if (!is_dir($sessionDirectory) && !mkdir($sessionDirectory, 0775, true) && !is_dir($sessionDirectory)) {
        http_response_code(500);
        exit('Không thể tạo thư mục lưu phiên đăng nhập.');
    }

    session_save_path($sessionDirectory);
    session_start();
}

if (!defined('BASE_URL')) {
    $projectRoot = str_replace('\\', '/', dirname(__DIR__));
    $scriptFile = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
    $scriptUrl = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $relativeScript = str_starts_with($scriptFile, $projectRoot . '/')
        ? substr($scriptFile, strlen($projectRoot) + 1)
        : basename($scriptUrl);
    $urlRoot = str_replace('\\', '/', dirname($scriptUrl, substr_count($relativeScript, '/') + 1));
    define('BASE_URL', rtrim($urlRoot, '/.') . '/');
}

require_once __DIR__ . '/../config/database.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require_once $path;
    }
});

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL ảnh đại diện, hoặc null nếu người dùng chưa có ảnh. */
function avatarUrl(?string $avatar): ?string
{
    $name = basename((string) $avatar);
    return $name === '' ? null : BASE_URL . 'assets/uploads/avatars/' . rawurlencode($name);
}

/** Nội dung bên trong ô avatar: ảnh nếu có, nếu không thì chữ cái đầu của tên. */
function avatarInner(?string $name, ?string $avatar): string
{
    $url = avatarUrl($avatar);
    if ($url !== null) {
        return '<img src="' . e($url) . '" alt="" loading="lazy">';
    }
    $initial = mb_strtoupper(mb_substr(trim((string) $name), 0, 1));
    return e($initial !== '' ? $initial : '?');
}

/** Ô avatar tròn dùng ở chỗ chưa có khung riêng. $size: xs | sm | md | lg. */
function avatarTag(?string $name, ?string $avatar, string $size = 'sm'): string
{
    return '<span class="nk-avatar nk-avatar-' . e($size) . '" aria-hidden="true">' . avatarInner($name, $avatar) . '</span>';
}

/** Thông báo chưa đọc của người đang đăng nhập (đọc xong là đánh dấu đã đọc). */
function notificationBanner(): string
{
    $user = $_SESSION['user'] ?? null;
    if (!is_array($user) || empty($user['id'])) {
        return '';
    }
    try {
        $messages = (new \App\Models\UserNotification(db()))->pullUnread((int) $user['id']);
    } catch (\Throwable) {
        return ''; // Chưa chạy migration hoặc mất kết nối: bỏ qua, không làm hỏng trang.
    }
    if ($messages === []) {
        return '';
    }
    $html = '<div class="nk-notices" role="status">';
    foreach ($messages as $message) {
        $html .= '<div class="nk-notice"><span>' . e($message) . '</span>'
            . '<button type="button" class="nk-notice-close" aria-label="Đóng thông báo" onclick="this.parentNode.remove()">✕</button></div>';
    }
    return $html . '</div>';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $sentToken = (string) ($_POST['csrf_token'] ?? '');
    $savedToken = (string) ($_SESSION['csrf_token'] ?? '');
    if ($savedToken === '' || !hash_equals($savedToken, $sentToken)) {
        http_response_code(419);
        exit('Phiên làm việc không hợp lệ. Vui lòng quay lại và thử lại.');
    }
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

require_once __DIR__ . '/../includes/public-posts.php';
