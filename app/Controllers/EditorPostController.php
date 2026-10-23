<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Post;
use App\Services\AuthService;
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
            if ($action === 'reject' && $note === '') {
                $_SESSION['editor_error'] = 'Vui lòng nhập lý do từ chối.';
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
        $this->render('editor.posts', compact(
            'filter', 'viewId', 'success', 'error', 'counts', 'posts', 'page', 'totalPages', 'selectedPost'
        ) + ['pageTitle' => 'Duyệt bài - Biên tập viên', 'pageCss' => 'editor-posts.css'], 'layouts.editor');
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
