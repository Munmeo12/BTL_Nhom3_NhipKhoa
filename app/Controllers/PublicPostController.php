<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Post;
use Closure;
use PDO;
use PDOException;

final class PublicPostController extends Controller
{
    public const CATEGORIES = [
        'tin-khoa' => 'Tin khoa',
        'hoc-tap' => 'Học tập & Nghiên cứu',
        'co-hoi' => 'Cơ hội',
        'su-kien' => 'Sự kiện',
    ];

    public function __construct(private readonly Closure $pdoFactory, \App\Core\View $view)
    {
        parent::__construct($view);
    }

    public function category(Request $request, string $slug): void
    {
        if (!isset(self::CATEGORIES[$slug])) {
            http_response_code(404);
            exit('404 - Không tìm thấy danh mục.');
        }
        $this->listing($request, $slug);
    }

    public function search(Request $request): void
    {
        $this->listing($request, null);
    }

    private function listing(Request $request, ?string $category): void
    {
        $isSearch = $category === null;
        $keyword = $isSearch ? trim((string) $request->query('q', '')) : '';
        $requestedPage = max(1, (int) $request->query('page', 1));
        $data = ['posts' => [], 'total' => 0, 'pages' => 1, 'page' => 1];
        $latest = [];
        $loadError = false;

        try {
            $posts = new Post($this->pdo());
            $data = $posts->publishedList($category, $keyword, $requestedPage);
            if (!$isSearch) {
                $latest = array_slice($posts->publishedList(null, '', 1)['posts'], 0, 3);
            }
        } catch (PDOException) {
            $loadError = true;
            http_response_code(503);
        }

        $descriptions = [
            'tin-khoa' => 'Thông tin, hoạt động và thông báo mới nhất từ khoa.',
            'hoc-tap' => 'Kiến thức, nghiên cứu và kinh nghiệm học tập dành cho sinh viên.',
            'co-hoi' => 'Học bổng, thực tập, việc làm và cơ hội phát triển bản thân.',
            'su-kien' => 'Hội thảo, workshop và các hoạt động của khoa.',
        ];
        $pageTitle = $isSearch
            ? ($keyword === '' ? 'Tất cả bài viết' : 'Kết quả tìm kiếm')
            : self::CATEGORIES[$category];
        $context = [
            'from' => $category ?? 'tim-kiem',
            'q' => $keyword,
            'page' => $data['page'],
        ];

        $this->render('public.list', compact(
            'category', 'isSearch', 'keyword', 'requestedPage', 'data', 'latest',
            'loadError', 'descriptions', 'pageTitle', 'context'
        ) + [
            'activeNav' => $category ?? '',
            'publicStyles' => true,
            'categories' => self::CATEGORIES,
            'grid' => in_array($category, ['hoc-tap', 'su-kien'], true),
        ], 'layouts.public');
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}
