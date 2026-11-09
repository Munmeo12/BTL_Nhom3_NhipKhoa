<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\AuthorRequest;
use App\Models\User;
use App\Services\AuthService;
use Closure;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

/**
 * Luồng xin nâng quyền Độc giả -> Tác giả:
 *  - form(): Độc giả gửi yêu cầu và xem trạng thái của mình.
 *  - adminIndex(): Admin xem danh sách, duyệt hoặc từ chối kèm lý do.
 */
final class AuthorRequestController extends Controller
{
    private const ROLE_NAMES = [
        'admin' => 'Quản trị viên',
        'editor' => 'Biên tập viên',
        'author' => 'Tác giả',
        'reader' => 'Độc giả',
    ];

    public function __construct(
        private readonly Closure $pdoFactory,
        private readonly AuthService $auth,
        \App\Core\View $view
    ) {
        parent::__construct($view);
    }

    /* ------------------------------------------------------------------
     * Phía Độc giả
     * ------------------------------------------------------------------ */
    public function form(Request $request): void
    {
        $sessionUser = $this->auth->user();
        if ($sessionUser === null) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập để gửi yêu cầu làm tác giả.';
            Response::redirect(BASE_URL . 'dang-nhap.php');
        }

        $userId = (int) $sessionUser['id'];
        $values = ['contact' => '', 'topics' => '', 'reason' => '', 'sample_link' => '', 'agree' => ''];
        $errors = [];
        $latest = null;
        $history = [];
        $databaseError = '';
        $role = (string) ($sessionUser['role'] ?? 'reader');

        try {
            $users = new User($this->pdo());
            $account = $users->adminTarget($userId);
            if ($account === null) {
                $this->auth->logout();
                $_SESSION['flash_error'] = 'Tài khoản không còn tồn tại.';
                Response::redirect(BASE_URL . 'dang-nhap.php');
            }
            if ($account['status'] !== 'active') {
                Response::abort(403, '403 - Tài khoản đang bị khóa.');
            }

            // Vai trò trong DB là nguồn đúng: nếu Admin vừa duyệt thì cập nhật phiên
            // ngay, người dùng không cần đăng xuất rồi đăng nhập lại.
            $role = (string) $account['role'];
            if (isset(self::ROLE_NAMES[$role]) && ($sessionUser['role'] ?? '') !== $role) {
                $_SESSION['user']['role'] = $role;
            }

            $requests = new AuthorRequest($this->pdo());

            if ($request->isPost() && $role === 'reader') {
                verifyCsrf();
                $values = [
                    'contact' => trim((string) $request->input('contact', '')),
                    'topics' => trim((string) $request->input('topics', '')),
                    'reason' => trim((string) $request->input('reason', '')),
                    'sample_link' => trim((string) $request->input('sample_link', '')),
                    'agree' => (string) $request->input('agree', ''),
                ];
                $errors = $this->validate($values);

                if ($errors === []) {
                    if ($requests->create($userId, $values['contact'], $values['topics'], $values['reason'], $values['sample_link'])) {
                        $_SESSION['author_request_notice'] = 'Đã gửi yêu cầu. Quản trị viên sẽ xem xét và phản hồi trong thời gian sớm nhất.';
                        Response::redirect(BASE_URL . 'yeu-cau-tac-gia.php');
                    }
                    $errors['form'] = 'Bạn đang có một yêu cầu chờ duyệt. Hãy đợi quản trị viên xử lý xong.';
                }
            }

            $latest = $requests->latestForUser($userId);
            $history = $requests->historyForUser($userId);
        } catch (PDOException $exception) {
            $databaseError = $exception->getCode() === '42S02'
                ? 'Chưa có bảng author_requests. Hãy chạy file database/migrations/2026_10_23_add_author_requests.sql.'
                : 'Không thể xử lý yêu cầu lúc này. Hãy kiểm tra kết nối MySQL.';
        }

        $notice = (string) ($_SESSION['author_request_notice'] ?? '');
        unset($_SESSION['author_request_notice']);

        $this->render('author_request.form', compact(
            'values', 'errors', 'latest', 'history', 'databaseError', 'notice', 'role'
        ) + [
            'roleName' => self::ROLE_NAMES[$role] ?? $role,
            'pageTitle' => 'Đăng ký làm tác giả',
            'activeNav' => '',
            'publicStyles' => true,
        ], 'layouts.public');
    }

    /** Trả mảng lỗi theo tên trường; rỗng nếu hợp lệ. */
    private function validate(array $values): array
    {
        $errors = [];
        if (mb_strlen($values['contact']) > 100) {
            $errors['contact'] = 'Thông tin liên hệ tối đa 100 ký tự.';
        }
        $topicsLength = mb_strlen($values['topics']);
        if ($topicsLength < 3 || $topicsLength > 255) {
            $errors['topics'] = 'Chủ đề muốn viết cần từ 3 đến 255 ký tự.';
        }
        $reasonLength = mb_strlen($values['reason']);
        if ($reasonLength < 30 || $reasonLength > 1000) {
            $errors['reason'] = 'Lý do cần từ 30 đến 1000 ký tự (hiện có ' . $reasonLength . ').';
        }
        if ($values['sample_link'] !== '') {
            $scheme = strtolower((string) parse_url($values['sample_link'], PHP_URL_SCHEME));
            if (
                strlen($values['sample_link']) > 255
                || !filter_var($values['sample_link'], FILTER_VALIDATE_URL)
                || !in_array($scheme, ['http', 'https'], true)
            ) {
                $errors['sample_link'] = 'Link bài mẫu phải bắt đầu bằng http:// hoặc https:// và tối đa 255 ký tự.';
            }
        }
        if ($values['agree'] !== '1') {
            $errors['agree'] = 'Bạn cần xác nhận cam kết nội dung do chính mình viết.';
        }
        return $errors;
    }

    /* ------------------------------------------------------------------
     * Phía Admin
     * ------------------------------------------------------------------ */
    public function adminIndex(Request $request): void
    {
        $admin = $this->auth->requireRole(['admin']);
        $filter = $this->statusFilter((string) $request->query('status', 'pending'));

        if ($request->isPost()) {
            verifyCsrf();
            $filter = $this->statusFilter((string) $request->input('back_status', 'pending'));
            $id = (int) $request->input('request_id', 0);
            $action = (string) $request->input('action', '');
            try {
                if ($id <= 0) {
                    throw new RuntimeException('Yêu cầu không hợp lệ.');
                }
                $model = new AuthorRequest($this->pdo());
                $targetUser = $model->userIdOf($id);
                if ($action === 'approve') {
                    $result = $model->approve($id, (int) $admin['id']);
                    $this->notify($targetUser, $result === 'promoted'
                        ? 'Yêu cầu làm tác giả của bạn đã được duyệt. Bạn giờ là Tác giả và có thể viết bài.'
                        : 'Yêu cầu làm tác giả của bạn đã được duyệt.');
                    $_SESSION['admin_notice'] = $result === 'promoted'
                        ? 'Đã duyệt: tài khoản được nâng lên Tác giả.'
                        : 'Đã duyệt. Tài khoản này đã có quyền cao hơn Độc giả nên giữ nguyên vai trò.';
                } elseif ($action === 'reject') {
                    $note = trim((string) $request->input('admin_note', ''));
                    $length = mb_strlen($note);
                    if ($length < 5 || $length > 500) {
                        throw new RuntimeException('Lý do từ chối cần từ 5 đến 500 ký tự.');
                    }
                    $model->reject($id, (int) $admin['id'], $note);
                    $this->notify($targetUser, 'Yêu cầu làm tác giả của bạn bị từ chối. Lý do: ' . $note);
                    $_SESSION['admin_notice'] = 'Đã từ chối yêu cầu và gửi lý do cho người dùng.';
                } else {
                    throw new RuntimeException('Thao tác không hợp lệ.');
                }
            } catch (Throwable $exception) {
                $_SESSION['admin_error'] = $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'Không thể xử lý yêu cầu. Hãy thử lại.';
            }
            Response::redirect(BASE_URL . 'admin/yeu-cau-tac-gia.php?status=' . urlencode($filter));
        }

        $requests = [];
        $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        $databaseError = '';
        try {
            $model = new AuthorRequest($this->pdo());
            $counts = $model->counts();
            $requests = $model->listForAdmin($filter);
        } catch (PDOException $exception) {
            $databaseError = $exception->getCode() === '42S02'
                ? 'Chưa có bảng author_requests. Hãy chạy file database/migrations/2026_10_23_add_author_requests.sql.'
                : 'Không thể tải danh sách yêu cầu. Hãy kiểm tra kết nối MySQL.';
        }

        $notice = (string) ($_SESSION['admin_notice'] ?? '');
        $error = (string) ($_SESSION['admin_error'] ?? '');
        unset($_SESSION['admin_notice'], $_SESSION['admin_error']);

        $this->render('admin.author_requests', compact('requests', 'counts', 'filter', 'notice', 'error', 'databaseError') + [
            'roleNames' => self::ROLE_NAMES,
            'pageTitle' => 'Yêu cầu làm tác giả',
            'adminPage' => 'author-requests',
        ], 'layouts.admin');
    }

    /** Gửi thông báo cho người dùng; lỗi thông báo không được làm hỏng thao tác chính. */
    private function notify(?int $userId, string $message): void
    {
        if ($userId === null) {
            return;
        }
        try {
            (new \App\Models\UserNotification($this->pdo()))->add($userId, $message);
        } catch (Throwable) {
        }
    }

    private function statusFilter(string $value): string
    {
        return in_array($value, ['pending', 'approved', 'rejected', 'all'], true) ? $value : 'pending';
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
