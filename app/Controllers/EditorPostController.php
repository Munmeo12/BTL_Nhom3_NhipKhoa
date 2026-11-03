<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Post;
use App\Services\AuthService;
use App\Services\ImageUploadService;
use Closure;
use PDO;

final class EditorPostController extends Controller
{
    private const FILTERS = ['all', 'pending', 'published', 'rejected'];

    public function __construct(private readonly Closure $pdoFactory, private readonly AuthService $auth, \App\Core\View $view)
    {
        parent::__construct($view);
    }

    public function dashboard(): void
    {
        $this->auth->requireRole(['editor', 'admin']);
        $model = new Post($this->pdo());
        $stats = $model->editorCounts();
        $recentPosts = $model->pendingForEditor(3);
        $this->render('editor.dashboard', compact('stats', 'recentPosts') + [
            'pageTitle' => 'Tổng quan - Biên tập viên', 'pageCss' => 'editor-dashboard.css',
        ], 'layouts.editor');
    }

    public function posts(Request $request): void
    {
        $user = $this->auth->requireRole(['editor', 'admin']);
        $filter = (string) $request->query('status', 'all');
        if (!in_array($filter, self::FILTERS, true)) $filter = 'all';
        $viewId = (int) $request->query('view', 0);
        $model = new Post($this->pdo());

        if ($request->isPost()) {
            verifyCsrf();
            $postId = (int) $request->input('post_id', 0);
            $action = (string) $request->input('action', '');
            $note = trim((string) $request->input('editor_note', ''));
            $isAdmin = ($user['role'] ?? '') === 'admin';
            if ($action === 'reject' && $note === '') {
                $_SESSION['editor_error'] = 'Vui lòng nhập lý do từ chối.';
            } elseif ($action === 'unpublish' && $isAdmin) {
                if ($note === '') {
                    $_SESSION['editor_error'] = 'Vui lòng nhập lý do gỡ bài.';
                } elseif ($model->unpublish($postId, (int) $user['id'], $note)) {
                    $_SESSION['editor_success'] = 'Đã gỡ bài viết khỏi trang công khai.';
                }
            } elseif ($action === 'delete' && $isAdmin) {
                $target = $model->findForReview($postId);
                if ($target !== null && $model->deleteAny($postId)) {
                    (new ImageUploadService(dirname(__DIR__, 2) . '/assets/uploads'))->delete($target['thumbnail'] ?? null);
                    $_SESSION['editor_success'] = 'Đã xóa bài viết.';
                    $postId = 0;
                } else {
                    $_SESSION['editor_error'] = 'Không thể xóa bài viết.';
                }
            } elseif (in_array($action, ['approve', 'reject'], true)) {
                $changed = $model->review($postId, (int) $user['id'], $action, $note);
                if ($changed) {
                    $_SESSION['editor_success'] = $action === 'approve'
                        ? 'Đã duyệt bài viết thành công.' : 'Đã từ chối bài viết.';
                }
            }
            Response::redirect(BASE_URL . 'editor/posts.php?' . http_build_query(['status' => $filter, 'view' => $postId]));
        }

        $success = (string) ($_SESSION['editor_success'] ?? '');
        $error = (string) ($_SESSION['editor_error'] ?? '');
        unset($_SESSION['editor_success'], $_SESSION['editor_error']);
        $counts = $model->editorCounts();
        $data = $model->forEditor($filter, max(1, (int) $request->query('page', 1)));
        $posts = $data['posts'];
        $page = $data['page'];
        $totalPages = $data['pages'];
        $selectedPost = $viewId > 0 ? $model->findForReview($viewId) : null;
        $viewData = compact(
            'filter', 'viewId', 'success', 'error', 'counts', 'posts', 'page', 'totalPages', 'selectedPost'
        );
        // Admin vào từ sidebar quản trị thì dùng layout admin cho đồng nhất với các mục khác.
        if (($user['role'] ?? '') === 'admin') {
            $this->render('admin.posts', $viewData + ['pageTitle' => 'Quản lý bài viết', 'adminPage' => 'posts'], 'layouts.admin');
            return;
        }
        $this->render('editor.posts', $viewData + ['pageTitle' => 'Duyệt bài - Biên tập viên', 'pageCss' => 'editor-posts.css'], 'layouts.editor');
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
