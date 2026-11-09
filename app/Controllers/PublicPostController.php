<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
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
        $found = null;
        // 4 chuyên mục gốc luôn dùng được; chuyên mục khác phải có trong DB và đang hiển thị.
        if (!isset(self::CATEGORIES[$slug])) {
            try {
                $found = $slug === '' ? null : (new Category($this->pdo()))->findActiveBySlug($slug);
            } catch (PDOException) {
                http_response_code(503);
                exit('Chưa thể tải danh mục. Vui lòng thử lại sau.');
            }
            if ($found === null) {
                http_response_code(404);
                exit('404 - Không tìm thấy danh mục.');
            }
        }
        $this->listing($request, $slug, $found);
    }

    public function search(Request $request): void
    {
        $this->listing($request, null);
    }

    private function listing(Request $request, ?string $category, ?array $found = null): void
    {
        $isSearch = $category === null;
        $keyword = $isSearch ? trim((string) $request->query('q', '')) : '';
        $tag = $isSearch ? trim((string) $request->query('tag', '')) : '';
        $tagName = '';
        $sort = (string) $request->query('sort', 'newest') === 'oldest' ? 'oldest' : 'newest';
        // Giữ lựa chọn sắp xếp khi chuyển trang.
        $filterQuery = $sort === 'oldest' ? ['sort' => 'oldest'] : [];
        $requestedPage = max(1, (int) $request->query('page', 1));
        $data = ['posts' => [], 'total' => 0, 'pages' => 1, 'page' => 1];
        $latest = [];
        $loadError = false;

        try {
            $posts = new Post($this->pdo());
            if ($tag !== '') {
                $found = (new Tag($this->pdo()))->findBySlug($tag);
                $tagName = (string) ($found['name'] ?? '');
            }
            $data = $posts->publishedList($category, $keyword, $requestedPage, 6, $tag !== '' ? $tag : null, $sort);
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
        if ($category !== null && !isset($descriptions[$category])) {
            $description = trim((string) ($found['description'] ?? ''));
            $descriptions[$category] = $description !== '' ? $description : 'Các bài viết thuộc chuyên mục này.';
        }
        $pageTitle = $isSearch
            ? ($tag !== '' ? 'Thẻ: ' . ($tagName !== '' ? $tagName : $tag) : ($keyword === '' ? 'Tất cả bài viết' : 'Kết quả tìm kiếm'))
            : (self::CATEGORIES[$category] ?? (string) ($found['name'] ?? $category));
        $categories = self::CATEGORIES;
        foreach (publicExtraCategories() as $extra) {
            $categories[$extra['slug']] = $extra['name'];
        }
        // Trang danh mục thêm mới dùng ?slug=..., phân trang phải giữ lại tham số này.
        $baseQuery = ($category !== null && !isset(self::CATEGORIES[$category])) ? ['slug' => $category] : [];
        $context = [
            'from' => $category ?? 'tim-kiem',
            'q' => $keyword,
            'page' => $data['page'],
        ];
        if ($tag !== '') {
            $context['tag'] = $tag;
        }

        $this->render('public.list', compact(
            'category', 'isSearch', 'keyword', 'tag', 'requestedPage', 'data', 'latest',
            'loadError', 'descriptions', 'pageTitle', 'context', 'baseQuery', 'sort', 'filterQuery'
        ) + [
            'activeNav' => $category ?? '',
            'publicStyles' => true,
            'categories' => $categories,
        ], 'layouts.public');
    }

    /** Nguồn cấp RSS 20 bài đã xuất bản mới nhất. */
    public function rss(): void
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $site = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL;
        $xml = static fn(string $text): string => htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        try {
            $posts = (new Post($this->pdo()))->publishedList(null, '', 1, 20)['posts'];
        } catch (PDOException) {
            http_response_code(503);
            exit('Không thể tải nguồn RSS.');
        }

        header('Content-Type: application/rss+xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<rss version="2.0"><channel>';
        echo '<title>Nhịp Khoa</title><link>' . $xml($site) . '</link>';
        echo '<description>Bài viết mới nhất từ Nhịp Khoa</description><language>vi</language>';
        foreach ($posts as $post) {
            $link = $site . 'bai-viet.php?id=' . (int) $post['id'];
            $time = strtotime((string) ($post['published_at'] ?: $post['created_at']));
            echo '<item><title>' . $xml((string) $post['title']) . '</title>';
            echo '<link>' . $xml($link) . '</link><guid>' . $xml($link) . '</guid>';
            echo '<description>' . $xml((string) $post['summary']) . '</description>';
            echo '<category>' . $xml((string) $post['category_name']) . '</category>';
            echo '<pubDate>' . date(DATE_RSS, $time ?: time()) . '</pubDate></item>';
        }
        echo '</channel></rss>';
    }

    private function pdo(): PDO
    {
        return ($this->pdoFactory)();
    }
}