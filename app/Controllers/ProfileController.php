<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\User;
use App\Services\AuthService;
use App\Services\ImageUploadService;
use Closure;
use PDO;
use PDOException;
use RuntimeException;

/**
 * Trang cá nhân: đổi tên hiển thị, đổi mật khẩu, đổi/xóa ảnh đại diện.
 * Dành cho Độc giả, Tác giả và Biên tập viên (Admin không dùng trang này).
 */
final class ProfileController extends Controller
{
    private const ROLES = ['reader', 'author', 'editor'];

    public function __construct(
        private readonly Closure $pdoFactory,
        private readonly AuthService $auth,
        private readonly ImageUploadService $avatars,
        View $view
    ) {
        parent::__construct($view);
    }

    public function show(Request $request): void
    {
        $current = $this->auth->user();
        if ($current !== null && ($current['role'] ?? '') === 'admin') {
            Response::redirect(BASE_URL . 'admin/dashboard.php');
        }
        $sessionUser = $this->auth->requireRole(self::ROLES);
        $id = (int) $sessionUser['id'];

        $errors = [];
        $name = null;
        $users = null;

        try {
            $users = new User($this->pdo());
            if ($request->isPost()) {
                verifyCsrf();
                $action = (string) $request->input('action', '');
                $result = match ($action) {
                    'update_name' => $this->updateName($request, $users, $id, $name),
                    'change_password' => $this->changePassword($request, $users, $id),
                    'update_avatar' => $this->updateAvatar($request, $users, $id),
                    'remove_avatar' => $this->removeAvatar($users, $id),
                    default => ['error' => ['form', 'Thao tác không hợp lệ.']],
                };
                if (isset($result['error'])) {
                    $errors[$result['error'][0]] = $result['error'][1];
                } else {
                    $_SESSION['profile_notice'] = (string) $result['notice'];
                    Response::redirect(BASE_URL . 'trang-ca-nhan.php');
                }
            }
            $profile = $users->profile($id);
        } catch (PDOException) {
            $profile = null;
            $errors['form'] = 'Không thể tải hoặc lưu hồ sơ. Hãy chạy migration 2026_11_07_user_avatar.sql và kiểm tra kết nối MySQL.';
        }

        if ($profile === null) {
            $profile = [
                'id' => $id,
                'email' => (string) ($sessionUser['email'] ?? ''),
                'full_name' => (string) ($sessionUser['full_name'] ?? ''),
                'avatar' => $sessionUser['avatar'] ?? null,
                'created_at' => null,
                'role' => (string) ($sessionUser['role'] ?? 'reader'),
                'role_name' => '',
            ];
        }

        $notice = (string) ($_SESSION['profile_notice'] ?? '');
        unset($_SESSION['profile_notice']);
        $nameValue = $name ?? (string) $profile['full_name'];
        $role = (string) $profile['role'];
        $layout = match ($role) {
            'author' => 'layouts.author',
            'editor' => 'layouts.editor',
            default => 'layouts.public',
        };

        $this->render(
            'profile.show',
            compact('profile', 'errors', 'notice', 'nameValue', 'role')
                + ['pageTitle' => 'Trang cá nhân', 'adminPage' => 'profile', 'pageCss' => 'profile.css', 'activeNav' => ''],
            $layout
        );
    }

    /** @return array{notice?: string, error?: array{0: string, 1: string}} */
    private function updateName(Request $request, User $users, int $id, ?string &$name): array
    {
        $name = trim((string) preg_replace('/\s+/u', ' ', (string) $request->input('full_name', '')));
        $length = mb_strlen($name);
        if ($length < 2 || $length > 120) {
            return ['error' => ['full_name', 'Tên hiển thị phải có từ 2 đến 120 ký tự.']];
        }
        $users->updateName($id, $name);
        $_SESSION['user']['full_name'] = $name;
        return ['notice' => 'Đã cập nhật tên hiển thị.'];
    }

    /** @return array{notice?: string, error?: array{0: string, 1: string}} */
    private function changePassword(Request $request, User $users, int $id): array
    {
        $current = (string) $request->input('current_password', '');
        $new = (string) $request->input('new_password', '');
        $confirmation = (string) $request->input('new_password_confirmation', '');

        $hash = $users->passwordHash($id);
        if ($current === '' || $hash === null || !password_verify($current, $hash)) {
            return ['error' => ['current_password', 'Mật khẩu hiện tại không đúng.']];
        }
        if (strlen($new) < 8 || strlen($new) > 72) {
            return ['error' => ['new_password', 'Mật khẩu mới phải có từ 8 đến 72 ký tự.']];
        }
        if (
            !preg_match('/[a-z]/', $new)
            || !preg_match('/[A-Z]/', $new)
            || !preg_match('/\d/', $new)
            || !preg_match('/[^A-Za-z0-9\s]/', $new)
        ) {
            return ['error' => ['new_password', 'Mật khẩu mới phải có chữ hoa, chữ thường, số và ký tự đặc biệt (ví dụ: @ # $ !).']];
        }
        if ($new === $current) {
            return ['error' => ['new_password', 'Mật khẩu mới phải khác mật khẩu hiện tại.']];
        }
        if ($new !== $confirmation) {
            return ['error' => ['new_password_confirmation', 'Mật khẩu nhập lại chưa khớp.']];
        }

        $users->updatePassword($id, password_hash($new, PASSWORD_DEFAULT));
        session_regenerate_id(true);
        return ['notice' => 'Đã đổi mật khẩu.'];
    }

    /** @return array{notice?: string, error?: array{0: string, 1: string}} */
    private function updateAvatar(Request $request, User $users, int $id): array
    {
        $profile = $users->profile($id);
        $stored = null;
        try {
            $stored = $this->avatars->store($request->file('avatar'));
            if ($stored === null) {
                return ['error' => ['avatar', 'Vui lòng chọn một ảnh để tải lên.']];
            }
            $users->updateAvatar($id, $stored);
        } catch (PDOException $exception) {
            // PDOException cũng là RuntimeException: phải bắt trước để không lộ lỗi SQL ra giao diện.
            $this->avatars->delete($stored);
            throw $exception;
        } catch (RuntimeException $exception) {
            return ['error' => ['avatar', $exception->getMessage()]];
        }
        $this->avatars->delete($profile['avatar'] ?? null);
        $_SESSION['user']['avatar'] = $stored;
        return ['notice' => 'Đã cập nhật ảnh đại diện.'];
    }

    /** @return array{notice?: string, error?: array{0: string, 1: string}} */
    private function removeAvatar(User $users, int $id): array
    {
        $profile = $users->profile($id);
        $users->updateAvatar($id, null);
        $this->avatars->delete($profile['avatar'] ?? null);
        $_SESSION['user']['avatar'] = null;
        return ['notice' => 'Đã xóa ảnh đại diện.'];
    }

    private function pdo(): PDO
    {
        $pdo = ($this->pdoFactory)();
        if (!$pdo instanceof PDO) {
            throw new RuntimeException('PDO factory must return PDO.');
        }
        return $pdo;
    }
}
