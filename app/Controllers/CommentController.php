<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Comment;
use App\Services\AuthService;
use Closure;
use PDO;

final class CommentController extends Controller
{
    private const STATUSES = ['approved', 'hidden'];

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

        $model = new Comment($this->pdo());
        $keyword = trim((string) $request->query('keyword', ''));
        $filter = (string) $request->query('status', 'all');
        $postId = (int) $request->query('post_id', 0);

        $this->render('admin.comments.index', [
            'keyword' => $keyword,
            'filter' => $filter,
            'postId' => $postId,
            'danhSachBaiViet' => $model->posts(),
            'binhLuan' => $model->search($keyword, $filter, $postId),
            'csrfToken' => csrfToken(),
            'thongBao' => '',
            'pageTitle' => 'Quản lý bình luận',
            'adminPage' => 'comments',
        ], 'layouts.admin');
    }

    public function detail(Request $request): void
    {
        $this->auth->requireRole(['admin']);

        $comment = (new Comment($this->pdo()))->findDetailed((int) $request->query('id', 0));
        if ($comment === null) {
            Response::abort(404, 'Không tìm thấy bình luận.');
        }

        $this->render('admin.comments.detail', [
            'comment' => $comment,
            'csrfToken' => csrfToken(),
        ]);
    }

    /**
     * Endpoint JSON cho các nút Duyệt / Ẩn / Hiện / Xóa trên trang quản lý.
     */
    public function moderateJson(Request $request): void
    {
        $this->auth->requireRole(['admin']);

        if (!$request->isPost()) {
            Response::json(['success' => false, 'message' => 'Phương thức không được phép.'], 405);
        }

        verifyCsrf();

        $id = (int) $request->input('comment_id', 0);
        if ($id <= 0) {
            Response::json(['success' => false, 'message' => 'ID bình luận không hợp lệ.'], 400);
        }

        $model = new Comment($this->pdo());
        $action = (string) $request->input('action', '');

        if ($action === 'delete') {
            $done = $model->delete($id);
            Response::json([
                'success' => $done,
                'message' => $done ? 'Xóa bình luận thành công.' : 'Không thể xóa bình luận.',
            ], $done ? 200 : 500);
        }

        $status = (string) $request->input('status', '');
        if (!in_array($status, self::STATUSES, true)) {
            Response::json(['success' => false, 'message' => 'Trạng thái không hợp lệ.'], 400);
        }

        $done = $model->updateStatus($id, $status);
        $messages = [
            'hide' => 'Ẩn bình luận thành công.',
            'show' => 'Hiển thị bình luận thành công.',
            'approve' => 'Duyệt bình luận thành công.',
        ];

        Response::json([
            'success' => $done,
            'message' => $done
                ? ($messages[$action] ?? 'Cập nhật trạng thái bình luận thành công.')
                : 'Không thể cập nhật trạng thái bình luận.',
            'status' => $status,
        ], $done ? 200 : 500);
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
