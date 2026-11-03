<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Tag;
use App\Services\AuthService;
use App\Services\Slugger;
use Closure;
use PDO;
use PDOException;

/**
 * CRUD thẻ (tags) trong khu vực quản trị.
 */
final class AdminTagController extends Controller
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
        $model = new Tag($this->pdo());

        if ($request->isPost()) {
            verifyCsrf();
            $this->handle($request, $model);
        }

        $editId = (int) $request->query('edit', 0);
        $editing = $editId > 0 ? $model->find($editId) : null;
        $success = (string) ($_SESSION['tag_success'] ?? '');
        $error = (string) ($_SESSION['tag_error'] ?? '');
        $formName = (string) ($_SESSION['tag_form_name'] ?? '');
        unset($_SESSION['tag_success'], $_SESSION['tag_error'], $_SESSION['tag_form_name']);

        $this->render('admin.tags', [
            'tags' => $model->all(),
            'editing' => $editing,
            'formName' => $formName !== '' ? $formName : (string) ($editing['name'] ?? ''),
            'success' => $success,
            'error' => $error,
            'pageTitle' => 'Quản lý thẻ',
            'adminPage' => 'tags',
        ], 'layouts.admin');
    }

    private function handle(Request $request, Tag $model): never
    {
        $action = (string) $request->input('action', '');
        $id = (int) $request->input('tag_id', 0);

        if ($action === 'delete') {
            if ($id > 0 && $model->delete($id)) {
                $_SESSION['tag_success'] = 'Đã xóa thẻ.';
            } else {
                $_SESSION['tag_error'] = 'Không thể xóa thẻ.';
            }
            Response::redirect(BASE_URL . 'admin/the.php');
        }

        $name = trim(preg_replace('/\s+/u', ' ', (string) $request->input('name', '')) ?? '');
        $slug = Slugger::slugify($name, 80);
        $back = BASE_URL . 'admin/the.php' . ($id > 0 ? '?edit=' . $id : '');

        if (mb_strlen($name) < 2 || mb_strlen($name) > 50 || $slug === '') {
            $_SESSION['tag_error'] = 'Tên thẻ phải có từ 2 đến 50 ký tự và chứa chữ hoặc số.';
            $_SESSION['tag_form_name'] = $name;
            Response::redirect($back);
        }
        if ($model->exists($name, $slug, $id)) {
            $_SESSION['tag_error'] = 'Thẻ này đã tồn tại (trùng tên hoặc slug).';
            $_SESSION['tag_form_name'] = $name;
            Response::redirect($back);
        }

        try {
            if ($id > 0) {
                $model->update($id, $name, $slug);
                $_SESSION['tag_success'] = 'Đã cập nhật thẻ.';
            } else {
                $model->create($name, $slug);
                $_SESSION['tag_success'] = 'Đã thêm thẻ mới.';
            }
        } catch (PDOException) {
            $_SESSION['tag_error'] = 'Không thể lưu thẻ. Tên hoặc slug có thể đã tồn tại.';
            $_SESSION['tag_form_name'] = $name;
            Response::redirect($back);
        }
        Response::redirect(BASE_URL . 'admin/the.php');
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
