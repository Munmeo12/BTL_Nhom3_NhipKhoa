<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Announcement;
use App\Services\AuthService;
use Closure;
use PDO;
use PDOException;

/**
 * Quản lý thông báo chạy ở đầu trang (chỉ admin).
 */
final class AdminAnnouncementController extends Controller
{
    public function __construct(
        private readonly Closure $pdoFactory,
        private readonly AuthService $auth,
        View $view
    ) {
        parent::__construct($view);
    }

    public function index(Request $request): void
    {
        $this->auth->requireRole(['admin']);
        $model = new Announcement(($this->pdoFactory)());

        if ($request->isPost()) {
            verifyCsrf();
            $this->handle($request, $model);
        }

        $editId = (int) $request->query('edit', 0);
        $editing = $editId > 0 ? $model->find($editId) : null;
        $success = (string) ($_SESSION['ann_success'] ?? '');
        $error = (string) ($_SESSION['ann_error'] ?? '');
        $formContent = (string) ($_SESSION['ann_form'] ?? '');
        unset($_SESSION['ann_success'], $_SESSION['ann_error'], $_SESSION['ann_form']);

        $this->render('admin.announcements', [
            'items' => $model->all(),
            'editing' => $editing,
            'formContent' => $formContent !== '' ? $formContent : (string) ($editing['content'] ?? ''),
            'success' => $success,
            'error' => $error,
            'pageTitle' => 'Thông báo chạy',
            'adminPage' => 'announcements',
        ], 'layouts.admin');
    }

    private function handle(Request $request, Announcement $model): never
    {
        $action = (string) $request->input('action', '');
        $id = (int) $request->input('announcement_id', 0);
        $list = BASE_URL . 'admin/thong-bao.php';

        if ($action === 'delete') {
            $_SESSION[$id > 0 && $model->delete($id) ? 'ann_success' : 'ann_error'] =
                $id > 0 ? 'Đã xóa thông báo.' : 'Không thể xóa thông báo.';
            Response::redirect($list);
        }
        if ($action === 'toggle') {
            if ($id > 0 && $model->toggle($id)) {
                $_SESSION['ann_success'] = 'Đã đổi trạng thái hiển thị.';
            }
            Response::redirect($list);
        }

        $content = trim(preg_replace('/\s+/u', ' ', (string) $request->input('content', '')) ?? '');
        $back = $list . ($id > 0 ? '?edit=' . $id : '');
        if (mb_strlen($content) < 5 || mb_strlen($content) > 255) {
            $_SESSION['ann_error'] = 'Nội dung thông báo phải từ 5 đến 255 ký tự.';
            $_SESSION['ann_form'] = $content;
            Response::redirect($back);
        }

        try {
            if ($id > 0) {
                $model->update($id, $content);
                $_SESSION['ann_success'] = 'Đã cập nhật thông báo.';
            } else {
                $model->create($content);
                $_SESSION['ann_success'] = 'Đã thêm thông báo mới.';
            }
        } catch (PDOException) {
            $_SESSION['ann_error'] = 'Không thể lưu thông báo. Hãy kiểm tra bảng announcements đã được tạo chưa.';
            $_SESSION['ann_form'] = $content;
            Response::redirect($back);
        }
        Response::redirect($list);
    }
}
