<?php

require_once __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| URL GỐC CỦA PROJECT
|--------------------------------------------------------------------------
*/

$basePath = BASE_URL;


/*
|--------------------------------------------------------------------------
| THÔNG TIN TRANG
|--------------------------------------------------------------------------
*/

$pageTitle = $pageTitle ?? 'Nhịp Khoa';

$user = $_SESSION['user'] ?? null;

$activeNav = $activeNav ?? (
    basename($_SERVER['SCRIPT_NAME'] ?? '') === 'index.php'
        ? 'home'
        : ''
);
?>
<!doctype html><html lang="vi"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> - Nhịp Khoa</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" data-site-css href="<?= e($basePath ?? '') ?>assets/css/style.css">
<?php if (!empty($publicStyles)): ?><link rel="stylesheet" href="<?= e($basePath ?? '') ?>assets/css/public-pages.css"><?php endif; ?>
</head><body>
<div class="announcement"><div class="site-shell"><span>Thông báo: Kỳ đăng ký học phần bắt đầu từ ngày 20/05.</span><a href="<?= e($basePath ?? '') ?>index.php#featured">Xem chi tiết →</a></div></div>
<header class="figma-header"><div class="site-shell header-inner">
    <a class="figma-logo" href="<?= e($basePath ?? '') ?>index.php"><img src="<?= e($basePath ?? '') ?>assets/images/figma/brand-logo.png" alt="Nhịp Khoa - The Faculty Post"></a>
    <nav class="desktop-nav" id="primary-navigation" aria-label="Điều hướng chính">
        <a <?= $activeNav === 'home' ? 'class="active" aria-current="page"' : '' ?> href="<?= e($basePath ?? '') ?>index.php">Trang chủ</a><a <?= $activeNav === 'tin-khoa' ? 'class="active" aria-current="page"' : '' ?> href="<?= e($basePath ?? '') ?>pages/tin-khoa.php">Tin khoa</a><a <?= $activeNav === 'hoc-tap' ? 'class="active" aria-current="page"' : '' ?> href="<?= e($basePath ?? '') ?>pages/hoc-tap.php">Học tập</a><a <?= $activeNav === 'co-hoi' ? 'class="active" aria-current="page"' : '' ?> href="<?= e($basePath ?? '') ?>pages/co-hoi.php">Cơ hội</a><a <?= $activeNav === 'su-kien' ? 'class="active" aria-current="page"' : '' ?> href="<?= e($basePath ?? '') ?>pages/su-kien.php">Sự kiện</a><a href="<?= e($basePath ?? '') ?>dang-phat-trien.php?feature=huong-dan">Hướng dẫn</a><a href="<?= e($basePath ?? '') ?>views/impact-box.php">Hộp tác động</a>
    </nav>
    <div class="header-actions"><form class="expanding-search" role="search" method="get" action="<?= e($basePath ?? '') ?>pages/tim-kiem.php"><input class="search-input" type="search" name="q" placeholder="Tìm kiếm bài viết..." aria-label="Nhập từ khóa tìm kiếm" autocomplete="off"><button class="search-circle" type="submit" aria-label="Mở tìm kiếm" aria-expanded="false"><img src="<?= e($basePath ?? '') ?>assets/images/figma/icon-search.svg" alt=""></button></form>
    <?php if ($user === null): ?><a class="login-pill" href="<?= e($basePath ?? '') ?>dang-nhap.php"><img src="<?= e($basePath ?? '') ?>assets/images/figma/icon-user.svg" alt="">Đăng nhập</a>
    <?php else: ?><span class="welcome">Chào, <?= e($user['full_name']) ?></span><?php if (in_array(($user['role'] ?? ''), ['admin','editor','author'], true)): ?><a href="<?= e($basePath ?? '') ?><?= e(roleLandingPage((string) $user['role'])) ?>"><?= ($user['role'] ?? '') === 'admin' ? 'Quản trị' : 'Khu vực của tôi' ?></a><?php endif; ?><form method="post" action="<?= e($basePath ?? '') ?>dang-xuat.php" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><button class="link-button logout-button" type="submit"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M14 3h4a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-4"/></svg><span>Đăng xuất</span></button></form><?php endif; ?>
    </div><button class="mobile-menu" type="button" aria-label="Mở menu" aria-expanded="false" aria-controls="primary-navigation"><span></span><span></span><span></span></button>
</div></header>
<main>
