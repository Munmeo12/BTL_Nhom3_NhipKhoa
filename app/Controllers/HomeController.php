<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ImpactBox;
use App\Models\Post;
use Closure;
use PDO;
use PDOException;

final class HomeController extends Controller
{
    public function __construct(private readonly Closure $pdoFactory, \App\Core\View $view)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $posts = [];
        $postsLoadError = false;
        $impactItems = [];
        $impactLoadError = false;

        try {
            $pdo = $this->pdo();
            $posts = (new Post($pdo))->publishedForHome(4);
        } catch (PDOException) {
            $postsLoadError = true;
        }

        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        if ($userId > 0) {
            try {
                $pdo ??= $this->pdo();
                $impactItems = array_slice((new ImpactBox($pdo))->forUser($userId), 0, 3);
            } catch (PDOException) {
                $impactLoadError = true;
            }
        }

        $this->render('public.home', compact(
            'posts',
            'postsLoadError',
            'impactItems',
            'impactLoadError'
        ) + [
            'pageTitle' => 'Trang chủ',
            'activeNav' => 'home',
        ], 'layouts.public');
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
