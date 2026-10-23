<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\AuthService;
use Closure;
use PDO;
use PDOException;

final class PublicArticleController extends Controller
{
    public function __construct(
        private readonly Closure $pdoFactory,
        private readonly AuthService $auth,
        \App\Core\View $view
    ) {
        parent::__construct($view);
    }

    public function show(Request $request): void
    {
        $postId = filter_var($request->query('id'), FILTER_VALIDATE_INT);
        $postId = is_int($postId) && $postId > 0 ? $postId : 0;
        $context = $this->context($request);
        $post = null;
        $related = [];
        $comments = [];
        $loadError = false;
        $commentError = '';
        $commentContent = '';
        $currentUser = $this->auth->user();

        if ($postId > 0) {
            try {
                $pdo = $this->pdo();
                $posts = new Post($pdo);
                $post = $posts->findPublished($postId);
                if ($post !== null) {
                    $comments = (new Comment($pdo))->approvedForPost($postId);
                    $related = $posts->relatedPublished((int) $post['category_id'], $postId);
                }
            } catch (PDOException) {
                $loadError = true;
            }
        }

        if ($post !== null && $request->isPost() && array_key_exists('comment_submit', $request->allInput())) {
            verifyCsrf();
            $commentContent = trim((string) $request->input('content', ''));

            if ($currentUser === null) {
                $commentError = 'Vui lòng đăng nhập để bình luận.';
            } elseif ($commentContent === '') {
                $commentError = 'Vui lòng nhập nội dung bình luận.';
            } elseif (mb_strlen($commentContent) > 1000) {
                $commentError = 'Bình luận không được vượt quá 1.000 ký tự.';
            } else {
                try {
                    $pdo ??= $this->pdo();
                    if ((new User($pdo))->status((int) $currentUser['id']) !== 'active') {
                        $commentError = 'Tài khoản của bạn không còn hoạt động nên không thể bình luận.';
                    } else {
                        (new Comment($pdo))->createPending($postId, (int) $currentUser['id'], $commentContent);
                        $_SESSION['comment_success'] = 'Bình luận đã được gửi và đang chờ duyệt.';
                        Response::redirect(publicDetailUrl($postId, $context, BASE_URL));
                    }
                } catch (PDOException) {
                    $commentError = 'Không thể lưu bình luận. Vui lòng thử lại.';
                }
            }
        }

        $commentSuccess = (string) ($_SESSION['comment_success'] ?? '');
        unset($_SESSION['comment_success']);

        if ($loadError) {
            http_response_code(503);
        } elseif ($post === null) {
            http_response_code(404);
        }

        $this->render('public.detail', compact(
            'postId', 'post', 'related', 'comments', 'loadError', 'commentError',
            'commentSuccess', 'commentContent', 'context', 'currentUser'
        ) + [
            'pageTitle' => $loadError ? 'Chưa thể tải bài viết' : ($post['title'] ?? 'Không tìm thấy bài viết'),
            'activeNav' => $post['category_slug'] ?? '',
            'publicStyles' => true,
        ], 'layouts.public');
    }

    private function context(Request $request): array
    {
        $from = trim((string) $request->query('from', ''));
        if (!isset(PublicPostController::CATEGORIES[$from]) && $from !== 'tim-kiem') {
            return [];
        }
        return [
            'from' => $from,
            'q' => trim((string) $request->query('q', '')),
            'page' => max(1, (int) $request->query('page', 1)),
        ];
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
