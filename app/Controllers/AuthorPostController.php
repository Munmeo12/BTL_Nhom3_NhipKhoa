<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;
use App\Models\Post;
use App\Services\AuthService;
use App\Services\ImageUploadService;
use Closure;
use PDO;
use Throwable;

final class AuthorPostController extends Controller
{
    private const STATUSES = ['draft', 'pending', 'published', 'rejected'];

    public function __construct(
        private readonly Closure $pdoFactory,
        private readonly AuthService $auth,
        private readonly ImageUploadService $uploads,
        \App\Core\View $view
    ) {
        parent::__construct($view);
    }

    public function dashboard(): void
    {
        $user = $this->auth->requireRole(['author']);
        $posts = new Post($this->pdo());
        $counts = $posts->authorCounts((int) $user['id']);
        $recentPosts = $posts->byAuthor((int) $user['id'], null, 3);
        $_SESSION['full_name'] = $user['full_name'];
        $this->render('author.dashboard', compact('counts', 'recentPosts', 'user') + [
            'pageTitle' => 'Trang tổng quan', 'pageCss' => 'dashboard.css',
        ], 'layouts.author');
    }

    public function posts(Request $request): void
    {
        $user = $this->auth->requireRole(['author']);
        $filter = (string) $request->query('status', 'all');
        $status = in_array($filter, self::STATUSES, true) ? $filter : null;
        $model = new Post($this->pdo());
        $counts = $model->authorCounts((int) $user['id']);
        $posts = $model->byAuthor((int) $user['id'], $status);
        $success = (string) ($_SESSION['success'] ?? '');
        unset($_SESSION['success']);
        $this->render('author.posts', compact('counts', 'posts', 'filter', 'success') + [
            'pageTitle' => 'Bài viết của tôi', 'pageCss' => 'posts.css',
        ], 'layouts.author');
    }

    public function view(Request $request): void
    {
        $user = $this->auth->requireRole(['author']);
        $postId = (int) $request->query('id', 0);
        if ($postId <= 0) {
            Response::abort(404, 'Bài viết không hợp lệ.');
        }
        $post = (new Post($this->pdo()))->findOwned($postId, (int) $user['id']);
        if ($post === null) {
            Response::abort(404, 'Không tìm thấy bài viết hoặc bạn không có quyền xem bài này.');
        }
        [$statusClass, $statusText] = $this->statusLabel((string) $post['status']);
        $postCode = 'BV' . str_pad((string) $post['id'], 3, '0', STR_PAD_LEFT);
        $success = (string) ($_SESSION['success'] ?? '');
        unset($_SESSION['success']);
        $this->render('author.view', compact('post', 'statusClass', 'statusText', 'postCode', 'success') + [
            'pageTitle' => 'Xem bài viết', 'pageCss' => 'view.css',
        ], 'layouts.author');
    }

    public function create(Request $request): void
    {
        $user = $this->auth->requireRole(['author']);
        $pdo = $this->pdo();
        $categories = (new Category($pdo))->forSelect();
        $values = ['title' => '', 'category_id' => '', 'summary' => '', 'content' => ''];
        $error = '';
        $success = '';

        if ($request->isPost()) {
            verifyCsrf();
            $values = $this->values($request);
            $error = $this->validate($values);
            $thumbnail = null;
            try {
                if ($error === '') {
                    $thumbnail = $this->uploads->store($request->file('thumbnail'));
                    $postId = (new Post($pdo))->create([
                        'author_id' => (int) $user['id'],
                        'category_id' => (int) $values['category_id'],
                        'title' => $values['title'],
                        'slug' => 'bai-viet-' . bin2hex(random_bytes(6)),
                        'summary' => $values['summary'],
                        'thumbnail' => $thumbnail,
                        'content' => $values['content'],
                        'status' => $values['action'] === 'submit' ? 'pending' : 'draft',
                    ]);
                    $_SESSION['success'] = $values['action'] === 'submit'
                        ? 'Bài viết đã được gửi duyệt thành công.'
                        : 'Bài viết đã được lưu nháp thành công.';
                    Response::redirect(BASE_URL . 'author/view.php?id=' . $postId);
                }
            } catch (Throwable $exception) {
                $this->uploads->delete($thumbnail);
                $error = $exception instanceof \RuntimeException
                    ? $exception->getMessage()
                    : 'Không thể lưu bài viết. Vui lòng thử lại.';
            }
        }

        $this->render('author.create', compact('categories', 'values', 'error', 'success') + [
            'pageTitle' => 'Tạo bài viết', 'pageCss' => 'create.css',
        ], 'layouts.author');
    }

    public function edit(Request $request): void
    {
        $user = $this->auth->requireRole(['author']);
        $postId = (int) $request->query('id', 0);
        $pdo = $this->pdo();
        $model = new Post($pdo);
        $post = $postId > 0 ? $model->findOwned($postId, (int) $user['id']) : null;
        if ($post === null) {
            Response::abort(404, 'Không tìm thấy bài viết hoặc bạn không có quyền chỉnh sửa bài này.');
        }
        if (!in_array($post['status'], ['draft', 'rejected'], true)) {
            Response::abort(403, 'Bài viết này không được phép chỉnh sửa.');
        }

        $categories = (new Category($pdo))->forSelect();
        $oldEditorNote = $post['editor_note'];
        $values = [
            'title' => (string) $post['title'], 'category_id' => (string) $post['category_id'],
            'summary' => (string) $post['summary'], 'content' => (string) $post['content'], 'action' => 'draft',
        ];
        $error = '';
        $success = '';

        if ($request->isPost()) {
            verifyCsrf();
            if (array_key_exists('delete_post', $request->allInput())) {
                try {
                    if (!$model->deleteOwned($postId, (int) $user['id'])) {
                        throw new \RuntimeException('Không thể xóa bài viết.');
                    }
                    $this->uploads->delete($post['thumbnail']);
                    $_SESSION['success'] = 'Bài viết đã được xóa thành công.';
                    Response::redirect(BASE_URL . 'author/posts.php');
                } catch (Throwable) {
                    $error = 'Không thể xóa bài viết. Bài có thể đang được dữ liệu khác sử dụng.';
                }
            } else {
                $values = $this->values($request);
                $error = $this->validate($values);
                $newThumbnail = null;
                try {
                    if ($error === '') {
                        $newThumbnail = $this->uploads->store($request->file('thumbnail'));
                        $thumbnail = $newThumbnail ?? $post['thumbnail'];
                        $submit = $values['action'] === 'submit';
                        $model->updateOwned($postId, (int) $user['id'], [
                            'category_id' => (int) $values['category_id'],
                            'title' => $values['title'], 'summary' => $values['summary'],
                            'thumbnail' => $thumbnail, 'content' => $values['content'],
                            'status' => $submit ? 'pending' : 'draft',
                        ], $submit);
                        if ($newThumbnail !== null) {
                            $this->uploads->delete($post['thumbnail']);
                        }
                        if ($submit) {
                            $_SESSION['success'] = 'Bài viết đã được cập nhật và gửi duyệt thành công.';
                            Response::redirect(BASE_URL . 'author/view.php?id=' . $postId);
                        }
                        $success = 'Bài viết đã được cập nhật và lưu nháp thành công.';
                        $post = $model->findOwned($postId, (int) $user['id']) ?? $post;
                        $values = [
                            'title' => (string) $post['title'], 'category_id' => (string) $post['category_id'],
                            'summary' => (string) $post['summary'], 'content' => (string) $post['content'], 'action' => 'draft',
                        ];
                    }
                } catch (Throwable $exception) {
                    $this->uploads->delete($newThumbnail);
                    $error = $exception instanceof \RuntimeException
                        ? $exception->getMessage()
                        : 'Không thể cập nhật bài viết. Vui lòng thử lại.';
                }
            }
        }

        $this->render('author.edit', compact(
            'post', 'postId', 'categories', 'oldEditorNote', 'values', 'error', 'success'
        ) + ['pageTitle' => 'Chỉnh sửa bài viết', 'pageCss' => 'edit.css'], 'layouts.author');
    }

    private function values(Request $request): array
    {
        return [
            'title' => trim((string) $request->input('title', '')),
            'category_id' => (string) (int) $request->input('category_id', 0),
            'summary' => trim((string) $request->input('summary', '')),
            'content' => trim((string) $request->input('content', '')),
            'action' => $request->input('action') === 'submit' ? 'submit' : 'draft',
        ];
    }

    private function validate(array $values): string
    {
        if ($values['title'] === '') return 'Vui lòng nhập tiêu đề bài viết.';
        if ((int) $values['category_id'] <= 0) return 'Vui lòng chọn chuyên mục.';
        if ($values['content'] === '') return 'Vui lòng nhập nội dung bài viết.';
        return '';
    }

    private function statusLabel(string $status): array
    {
        return match ($status) {
            'draft' => ['draft', 'Nháp'], 'pending' => ['pending', 'Chờ duyệt'],
            'published' => ['published', 'Đã đăng'], 'rejected' => ['rejected', 'Từ chối'],
            default => ['', 'Không xác định'],
        };
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
