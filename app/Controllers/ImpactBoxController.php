<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\ImpactBox;
use App\Services\AuthService;
use Closure;
use PDO;

final class ImpactBoxController extends Controller
{
    public function __construct(
        private readonly Closure $pdoFactory,
        private readonly AuthService $auth,
        \App\Core\View $view
    ) {
        parent::__construct($view);
    }

    public function index(Request $request): void
    {
        $user = $this->requireUser();
        $items = (new ImpactBox($this->pdo()))->forUser((int) $user['id']);
        $itemsPerPage = 8;
        $currentPage = max(1, (int) $request->query('page', 1));
        $totalItems = count($items);
        $totalPages = max(1, (int) ceil($totalItems / $itemsPerPage));
        $currentPage = min($currentPage, $totalPages);
        $pageItems = array_slice($items, ($currentPage - 1) * $itemsPerPage, $itemsPerPage);

        $this->render('impact.index', compact(
            'user', 'items', 'itemsPerPage', 'currentPage', 'totalItems', 'totalPages', 'pageItems'
        ) + [
            'pageTitle' => 'Impact Box',
            'activeNav' => 'impact-box',
            'publicStyles' => true,
        ], 'layouts.public');
    }

    public function action(Request $request): void
    {
        $user = $this->requireUser();
        if (!$request->isPost()) {
            Response::redirect(BASE_URL . 'views/impact-box.php');
        }
        verifyCsrf();

        $postId = (int) $request->input('post_id', 0);
        if ($postId <= 0) {
            Response::redirect(BASE_URL . 'views/impact-box.php');
        }

        $userId = (int) $user['id'];
        $model = new ImpactBox($this->pdo());
        $action = (string) $request->input('action', '');
        $note = $this->note((string) $request->input('note', ''));

        match ($action) {
            'add' => $this->add($model, $userId, $postId, $note),
            'delete' => $model->delete($userId, $postId),
            'update_note' => $model->updateNote($userId, $postId, $note),
            'clear_note' => $model->updateNote($userId, $postId, null),
            default => false,
        };

        Response::redirect(BASE_URL . 'views/impact-box.php');
    }

    private function add(ImpactBox $model, int $userId, int $postId, ?string $note): bool
    {
        if (!$model->canSavePost($postId) || $model->exists($userId, $postId)) {
            return false;
        }
        return $model->add($userId, $postId, $note);
    }

    private function note(string $note): ?string
    {
        $note = trim($note);
        return $note === '' ? null : mb_substr($note, 0, 200);
    }

    private function requireUser(): array
    {
        $user = $this->auth->user();
        if ($user === null || (int) ($user['id'] ?? 0) <= 0) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập để tiếp tục.';
            Response::redirect(BASE_URL . 'dang-nhap.php');
        }
        return $user;
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
