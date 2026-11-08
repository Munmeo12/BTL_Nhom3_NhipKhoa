<?php
declare(strict_types=1);
$basePath = BASE_URL;
$q = $keyword;
?>
<style>
.public-pages .public-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin:0 0 18px}
.public-pages .public-toolbar .public-count{margin:0}
.public-pages .public-sort{display:flex;align-items:center;gap:8px;margin:0;margin-left:auto}
.public-pages .public-sort label{font-size:13px;font-weight:600;color:#49576e}
.public-pages .public-sort select{height:38px;padding:0 10px;border:1px solid #ccd1db;border-radius:8px;background:#fff;font:14px Inter,Arial,sans-serif;color:#0e1625}
</style>
<section class="public-pages"><div class="site-shell">
<div class="public-heading">
    <div><p class="public-eyebrow">NHỊP KHOA · BÀI VIẾT</p><h1><?= e($pageTitle) ?></h1><p><?= e($isSearch ? (($tag ?? '') !== '' ? 'Các bài viết được gắn thẻ này.' : 'Tìm thông tin theo nội dung, danh mục, thẻ hoặc tác giả.') : $descriptions[$category]) ?></p></div>
    <?php if ($isSearch): ?><form class="public-search" action="tim-kiem.php" method="get" role="search"><label for="public-query">Từ khóa tìm kiếm</label><div><input id="public-query" name="q" type="search" value="<?= e($q) ?>" placeholder="Nhập từ khóa..."><button type="submit">Tìm kiếm</button></div></form><?php endif; ?>
</div>
<?php if ($loadError): ?>
<div class="public-empty" role="alert"><h2>Chưa thể tải bài viết</h2><p>Kết nối dữ liệu đang gián đoạn. Vui lòng thử lại sau.</p><a href="<?= e('?' . http_build_query($baseQuery + ['q' => $q, 'page' => $requestedPage] + $filterQuery)) ?>">Thử lại</a></div>
<?php else: ?>
<div class="public-toolbar">
<p class="public-count"><?= $data['total'] ?> bài viết<?= $q !== '' ? ' phù hợp với “' . e($q) . '”' : '' ?></p>
<form class="public-sort" method="get" aria-label="Sắp xếp bài viết">
<?php foreach ($baseQuery as $key => $value): ?><input type="hidden" name="<?= e((string) $key) ?>" value="<?= e((string) $value) ?>"><?php endforeach; ?>
<?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
<?php if (($tag ?? '') !== ''): ?><input type="hidden" name="tag" value="<?= e($tag) ?>"><?php endif; ?>
<label for="public-sort">Sắp xếp</label>
<select id="public-sort" name="sort" onchange="this.form.submit()"><option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option><option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option></select>
<noscript><button type="submit">Áp dụng</button></noscript>
</form>
</div>
<div class="public-layout<?= $isSearch ? ' public-layout-wide' : '' ?>">
<div>
<?php if (!$data['posts']): ?>
<div class="public-empty"><h2><?= $q !== '' ? 'Không tìm thấy bài viết' : 'Chưa có bài viết' ?></h2><p><?= $q !== '' ? 'Hãy thử một từ khóa khác.' : 'Các bài đã xuất bản sẽ xuất hiện tại đây.' ?></p></div>
<?php else: ?>
<div class="public-cards">
<?php foreach ($data['posts'] as $post): $url = publicDetailUrl((int) $post['id'], $context, $basePath); ?>
<article class="public-card">
<a class="public-card-image" href="<?= e($url) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(publicPostImage($post['thumbnail'], $basePath)) ?>" alt="" loading="lazy" data-public-image></a>
<div class="public-card-copy"><span class="public-category"><?= e($post['category_name']) ?></span><h2><a href="<?= e($url) ?>"><?= e($post['title']) ?></a></h2><p><?= e($post['summary']) ?></p><div class="public-card-meta"><span><?= e(publicPostDate($post)) ?> · <?= e($post['author_name']) ?></span><a href="<?= e($url) ?>">Xem bài →</a></div></div>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php if ($data['pages'] > 1): ?>
<nav class="public-pagination" aria-label="Phân trang bài viết">
<?php $pageUrl = static fn(int $n): string => '?' . http_build_query($baseQuery + ['q' => $q, 'page' => $n] + $filterQuery + (($tag ?? '') !== '' ? ['tag' => $tag] : [])); ?>
<?php if ($data['page'] > 1): ?><a href="<?= e($pageUrl($data['page'] - 1)) ?>">← Trước</a><?php endif; ?>
<?php for ($i = max(1, $data['page'] - 2); $i <= min($data['pages'], $data['page'] + 2); $i++): ?>
<a href="<?= e($pageUrl($i)) ?>" <?= $i === $data['page'] ? 'aria-current="page"' : '' ?>><?= $i ?></a>
<?php endfor; ?>
<?php if ($data['page'] < $data['pages']): ?><a href="<?= e($pageUrl($data['page'] + 1)) ?>">Sau →</a><?php endif; ?>
<span>Trang <?= $data['page'] ?>/<?= $data['pages'] ?></span>
</nav>
<?php endif; ?>
</div>
<?php if (!$isSearch): ?>
<aside class="public-sidebar"><section><h2>Danh mục</h2><nav aria-label="Danh mục bài viết">
<?php foreach ($categories as $slug => $name): ?><a href="<?= e(publicCategoryUrl((string) $slug, BASE_URL)) ?>" <?= (string) $slug === $category ? 'aria-current="page"' : '' ?>><?= e($name) ?></a><?php endforeach; ?>
</nav></section><section><h2>Bài mới nhất</h2>
<?php foreach ($latest as $post): ?><a class="public-related" href="<?= e(publicDetailUrl((int) $post['id'], $context, $basePath)) ?>"><img src="<?= e(publicPostImage($post['thumbnail'], $basePath)) ?>" alt="" data-public-image><span><?= e($post['title']) ?><small><?= e(publicPostDate($post)) ?></small></span></a><?php endforeach; ?>
<?php if (!$latest): ?><p>Chưa có bài viết.</p><?php endif; ?>
</section></aside>
<?php endif; ?>
</div>
<?php endif; ?>
</div></section>