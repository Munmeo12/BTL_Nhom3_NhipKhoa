<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Category;
use App\Services\AuthService;
use App\Services\CategoryValidator;
use Closure;
use PDO;

final class CategoryController extends Controller
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

        $keyword = trim((string) $request->query('keyword', ''));
        $status = (string) $request->query('status', 'all');
        if (!in_array($status, ['all', 'active', 'hidden'], true)) {
            $status = 'all';
        }

        $categories = (new Category($this->pdo()))->search($keyword, $status);

        $this->render('admin.categories.index', [
            'keyword' => $keyword,
            'status' => $status,
            'categories' => $categories,
            'pageTitle' => 'Quản lý danh mục',
            'adminPage' => 'categories',
        ], 'layouts.admin');
    }

    public function add(Request $request): void
    {
        $this->form($request, null);
    }

    public function edit(Request $request): void
    {
        $this->form($request, (int) $request->query('id', 0));
    }

    public function delete(Request $request): void
    {
        $this->auth->requireRole(['admin']);

        $id = (int) $request->query('id', 0);
        $model = new Category($this->pdo());
        $category = $model->find($id);
        if ($category === null) {
            Response::abort(404, 'Không tìm thấy danh mục.');
        }

        $validator = new CategoryValidator();
        $message = '';

        if ($request->isPost()) {
            verifyCsrf();

            if ((int) $category['post_count'] > 0) {
                $message = 'Không thể xóa danh mục đang có bài viết.';
            } elseif ($validator->write(static fn(): bool => $model->delete($id))) {
                Response::redirect(BASE_URL . 'views/categories.php');
            } else {
                $message = $validator->error();
            }
        }

        $this->render('admin.categories.delete', compact('category', 'message', 'id'));
    }

    /**
     * Form thêm mới ($id === null) và form sửa dùng chung một luồng xử lý.
     */
    private function form(Request $request, ?int $id): void
    {
        $this->auth->requireRole(['admin']);

        $model = new Category($this->pdo());
        $category = $id === null ? null : $model->find($id);
        if ($id !== null && $category === null) {
            Response::abort(404, 'Không tìm thấy danh mục.');
        }

        $values = [
            'name' => (string) ($category['name'] ?? ''),
            'slug' => (string) ($category['slug'] ?? ''),
            'description' => (string) ($category['description'] ?? ''),
            'status' => (string) ($category['status'] ?? 'active'),
        ];

        $validator = new CategoryValidator();
        $message = '';

        if ($request->isPost()) {
            verifyCsrf();

            foreach ($values as $key => $value) {
                $values[$key] = trim((string) $request->input($key, $value));
            }

            $saved = $validator->validate($values['name'], $values['slug'], $values['status'])
                && $validator->write(static fn(): bool => $id === null
                    ? $model->create($values)
                    : $model->update($id, $values));

            if ($saved) {
                Response::redirect(BASE_URL . 'views/categories.php');
            }

            $message = $validator->error();
        }

        // View kế thừa từ bản legacy dùng biến rời ($name, $slug, ...), và khóa
        // của $values trùng đúng tên đó nên trải thẳng vào dữ liệu render.
        $this->render(
            $id === null ? 'admin.categories.add' : 'admin.categories.edit',
            $values + compact('message', 'category', 'id')
        );
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
