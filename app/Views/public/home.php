<?php
declare(strict_types=1);
?>
<section class="figma-hero"><div class="site-shell hero-layout"><div class="hero-copy"><p>CẬP NHẬT · ĐỔI MỚI · TÁC ĐỘNG</p><h1>ĐIỀU GÌ<br>ĐANG THAY ĐỔI?</h1><i></i><div>Nơi cập nhật những thông tin quan trọng, cơ hội và hướng dẫn mới nhất dành riêng cho sinh viên Khoa CNTT.<br>Hiểu đúng – Hành động kịp thời – Tạo ra tác động.</div><div class="hero-buttons"><a href="#articles">Tìm thông tin →</a><a href="#featured">Impact Box</a></div></div><div class="hero-photo"><img src="assets/images/figma/home-hero.png" alt="Sinh viên trao đổi và học tập trong khuôn viên trường"></div></div></section>

<section id="featured" class="home-section home-impact-section"><div class="site-shell"><div class="section-title"><div><p class="section-kicker">KHÔNG GIAN CÁ NHÂN</p><h2>Impact Box của bạn</h2></div><a href="<?= empty($_SESSION['user']) ? 'dang-nhap.php' : 'views/impact-box.php' ?>"><?= empty($_SESSION['user']) ? 'Đăng nhập →' : 'Xem tất cả →' ?></a></div>
<?php if (empty($_SESSION['user'])): ?>
<div class="home-impact-state"><img src="assets/images/figma/icon-impact.svg" alt=""><div><h3>Lưu lại những bài viết quan trọng</h3><p>Đăng nhập để tạo Impact Box cá nhân, thêm ghi chú và xem lại khi cần.</p></div><a href="dang-nhap.php">Đăng nhập</a></div>
<?php elseif ($impactLoadError): ?>
<div class="home-impact-state" role="alert"><div><h3>Chưa thể tải Impact Box</h3><p>Kết nối dữ liệu đang gián đoạn. Vui lòng thử lại sau.</p></div></div>
<?php elseif (!$impactItems): ?>
<div class="home-impact-state"><img src="assets/images/figma/icon-impact.svg" alt=""><div><h3>Impact Box đang trống</h3><p>Nhấn biểu tượng ♡ tại một bài viết để lưu bài vào đây.</p></div><a href="#articles">Khám phá bài viết</a></div>
<?php else: ?>
<div class="home-impact-grid">
<?php foreach ($impactItems as $item): $impactUrl='bai-viet.php?id='.(int)$item['post_id']; ?>
<article class="home-impact-card"><a class="home-impact-image" href="<?= e($impactUrl) ?>"><img src="<?= e(publicPostImage($item['thumbnail'])) ?>" alt=""></a><div><span><?= e($item['category_name']) ?></span><h3><a href="<?= e($impactUrl) ?>"><?= e($item['title']) ?></a></h3><?php if (!empty($item['note'])): ?><p><?= e($item['note']) ?></p><?php else: ?><p class="home-impact-no-note">Chưa có ghi chú cho bài viết này.</p><?php endif; ?><small>Đã lưu <?= e(date('d/m/Y', strtotime((string)$item['created_at']))) ?></small></div></article>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div></section>

<section id="articles" class="home-section"><div class="site-shell"><div class="section-title"><h2>Bài viết và hướng dẫn</h2><a href="pages/tim-kiem.php">Xem tất cả →</a></div><div class="article-grid">
<?php if ($postsLoadError): ?>
<div class="article-empty" role="alert"><h3>Chưa thể tải bài viết</h3><p>Kết nối dữ liệu đang gián đoạn. Vui lòng thử lại sau.</p></div>
<?php elseif (!$posts): ?>
<div class="article-empty"><h3>Chưa có bài viết được xuất bản</h3><p>Bài viết sẽ xuất hiện tại đây sau khi được Biên tập viên duyệt.</p></div>
<?php else: ?>
<?php foreach ($posts as $post): $detailUrl='bai-viet.php?id='.(int)$post['id']; ?><article class="article-card"><a class="article-image-link" href="<?= e($detailUrl) ?>"><img src="<?= e(publicPostImage($post['thumbnail'])) ?>" alt=""></a><div><div class="article-meta"><span><?= e($post['category_name']) ?></span><span><?= e(date('d/m/Y',strtotime($post['published_at'] ?: $post['created_at']))) ?></span></div><h3><a href="<?= e($detailUrl) ?>"><?= e($post['title']) ?></a></h3><p><?= e($post['summary']) ?></p><div class="article-actions"><small>◷ 5 phút đọc</small><?php if (empty($_SESSION['user'])): ?><a class="save-button" href="dang-nhap.php" aria-label="Đăng nhập để lưu bài viết" title="Đăng nhập để lưu">♡</a><?php else: ?><form method="post" action="impact-box-action.php" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="add"><input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>"><button class="save-button" type="submit" aria-label="Lưu bài viết vào Impact Box" title="Lưu bài viết">♡</button></form><?php endif; ?></div></div></article><?php endforeach; ?>
<?php endif; ?>
</div></div></section>

<section id="topics" class="topic-section"><div class="site-shell topic-grid"><article><img src="assets/images/figma/icon-study.png" alt=""><div><h3>Học tập</h3><p>Lịch học, học phần, hướng dẫn học tập và tài liệu</p><a href="pages/hoc-tap.php">Khám phá →</a></div></article><article><img src="assets/images/figma/icon-opportunity.svg" alt=""><div><h3>Cơ hội</h3><p>Học bổng, tuyển dụng, thực tập và cuộc thi.</p><a href="pages/co-hoi.php">Khám phá →</a></div></article><article><img src="assets/images/figma/icon-event.svg" alt=""><div><h3>Sự kiện</h3><p>Hội thảo, workshop và hoạt động nổi bật.</p><a href="pages/su-kien.php">Khám phá →</a></div></article><article><img src="assets/images/figma/icon-impact.svg" alt=""><div><h3>Impact Box</h3><p>Các bài viết bạn đã lưu và ghi chú cá nhân.</p><a href="views/impact-box.php">Mở Impact Box →</a></div></article></div></section>
