<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';

const PUBLIC_CATEGORIES = [
    'tin-khoa' => 'Tin khoa',
    'hoc-tap' => 'Học tập & Nghiên cứu',
    'co-hoi' => 'Cơ hội',
    'su-kien' => 'Sự kiện',
];

function publicPostImage(?string $thumbnail, string $base = ''): string
{
    $fallback = $base . 'assets/images/figma/home-card-1.png';
    $path = trim($thumbnail ?? '');
    if ($path === '') return $fallback;
    if (filter_var($path, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($path, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true)) return $path;
    $path = str_replace('\\', '/', $path);
    if (str_contains($path, '..') || str_contains($path, ':') || str_starts_with($path, '//')) return $fallback;
    $path = ltrim($path, '/');
    if (!str_contains($path, '/')) $path = 'assets/uploads/' . $path;
    if (!str_starts_with($path, 'assets/') && !str_starts_with($path, 'uploads/')) return $fallback;
    return is_file(__DIR__ . '/../' . $path) ? $base . $path : $fallback;
}

function publicPostDate(array $post): string
{
    return date('d/m/Y', strtotime($post['published_at'] ?: $post['created_at']));
}

function publicDetailUrl(int $id, array $context = [], string $base = ''): string
{
    return $base . 'bai-viet.php?' . http_build_query(['id' => $id] + $context);
}

function publicBackUrl(array $context): string
{
    if (!$context) return 'index.php#articles';
    $query = ['q' => $context['q'], 'page' => $context['page']];
    if (!empty($context['tag'])) $query['tag'] = $context['tag'];
    $url = publicCategoryUrl((string) $context['from']);
    return $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($query);
}

/** 4 chuyên mục gốc có file riêng trong pages/; chuyên mục thêm mới dùng pages/danh-muc.php?slug=... */
function publicCategoryUrl(string $slug, string $base = ''): string
{
    if ($slug === 'tim-kiem' || isset(PUBLIC_CATEGORIES[$slug])) {
        return $base . 'pages/' . $slug . '.php';
    }
    return $base . 'pages/danh-muc.php?' . http_build_query(['slug' => $slug]);
}

/** Chuyên mục đang hiển thị nhưng không thuộc 4 chuyên mục gốc (cho menu "Chuyên mục khác"). */
function publicExtraCategories(): array
{
    try {
        $rows = (new App\Models\Category(db()))->activeList();
    } catch (Throwable) {
        return [];
    }
    return array_values(array_filter(
        $rows,
        static fn(array $row): bool => !isset(PUBLIC_CATEGORIES[$row['slug']])
    ));
}