<?php
declare(strict_types=1);

namespace App\Controllers;

use Closure;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\User;
use App\Services\AuthService;
use PDOException;
use RuntimeException;
use Throwable;

final class AuthController extends Controller
{
    public function __construct(
        private readonly Closure $userFactory,
        private readonly AuthService $auth,
        View $view
    ) {
        parent::__construct($view);
    }

    public function login(Request $request): void
    {
        if ($this->auth->loggedIn()) {
            Response::redirect(BASE_URL . $this->auth->landingPage((string) ($this->auth->user()['role'] ?? 'reader')));
        }

        $email = '';
        $errors = [];
        $accountLocked = false;
        $notice = (string) ($_SESSION['flash_success'] ?? '');
        $flashError = (string) ($_SESSION['flash_error'] ?? '');
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        if ($request->isPost()) {
            verifyCsrf();
            $email = strtolower(trim((string) $request->input('email', '')));
            $password = (string) $request->input('password', '');

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
                $errors['email'] = 'Vui lòng nhập email hợp lệ, tối đa 150 ký tự.';
            }
            if ($password === '' || strlen($password) < 8 || strlen($password) > 72) {
                $errors['password'] = 'Mật khẩu phải có từ 8 đến 72 ký tự.';
            }

            if ($errors === []) {
                try {
                    $user = $this->users()->findByEmail($email);
                    if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
                        $errors['login'] = 'Email hoặc mật khẩu không chính xác.';
                    } elseif ($user['status'] !== 'active') {
                        $accountLocked = true;
                        $errors['login'] = 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';
                    } else {
                        $this->auth->authenticate($user);
                        Response::redirect(BASE_URL . $this->auth->landingPage((string) $user['role']));
                    }
                } catch (PDOException) {
                    $errors['login'] = 'Chưa kết nối được cơ sở dữ liệu. Hãy kiểm tra MySQL và cấu hình.';
                }
            }
        }

        $this->render('auth.login', compact('email', 'errors', 'accountLocked', 'notice', 'flashError'), 'layouts.auth');
    }

    public function register(Request $request): void
    {
        if ($this->auth->loggedIn()) {
            Response::redirect(BASE_URL . 'index.php');
        }

        $values = ['full_name' => '', 'email' => ''];
        $errors = [];
        if ($request->isPost()) {
            verifyCsrf();
            $values['full_name'] = trim((string) $request->input('full_name', ''));
            $values['email'] = strtolower(trim((string) $request->input('email', '')));
            $password = (string) $request->input('password', '');
            $confirmation = (string) $request->input('password_confirmation', '');

            if (mb_strlen($values['full_name']) < 2 || mb_strlen($values['full_name']) > 120) {
                $errors['full_name'] = 'Họ tên phải có từ 2 đến 120 ký tự.';
            }
            if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || strlen($values['email']) > 150) {
                $errors['email'] = 'Vui lòng nhập email hợp lệ, tối đa 150 ký tự.';
            }
            if (strlen($password) < 8 || strlen($password) > 72) {
                $errors['password'] = 'Mật khẩu phải có từ 8 đến 72 ký tự.';
            } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
                $errors['password'] = 'Mật khẩu cần có ít nhất một chữ và một số.';
            }
            if ($password !== $confirmation) {
                $errors['password_confirmation'] = 'Mật khẩu nhập lại chưa khớp.';
            }

            if ($errors === []) {
                try {
                    if (!$this->users()->createReader($values['full_name'], $values['email'], password_hash($password, PASSWORD_DEFAULT))) {
                        throw new RuntimeException('Không tìm thấy role reader trong bảng roles.');
                    }
                    $_SESSION['flash_success'] = 'Đăng ký thành công. Bạn có thể đăng nhập ngay.';
                    Response::redirect(BASE_URL . 'dang-nhap.php');
                } catch (Throwable $exception) {
                    $errors['register'] = $exception instanceof PDOException && $exception->getCode() === '23000'
                        ? 'Email này đã được sử dụng.'
                        : 'Không thể đăng ký. Hãy kiểm tra kết nối cơ sở dữ liệu.';
                }
            }
        }

        $this->render('auth.register', compact('values', 'errors'), 'layouts.auth');
    }

    public function logout(Request $request): void
    {
        if (!$request->isPost()) {
            Response::abort(405, 'Chỉ chấp nhận phương thức POST.');
        }
        verifyCsrf();
        $this->auth->logout();
        Response::redirect(BASE_URL . 'dang-nhap.php');
    }

    private function users(): User
    {
        $users = ($this->userFactory)();
        if (!$users instanceof User) {
            throw new RuntimeException('User factory must return App\\Models\\User.');
        }
        return $users;
    }
}
