<?php
declare(strict_types=1);
$pageTitle = $pageTitle ?? 'Quản trị';
$adminPage = $adminPage ?? '';
$adminUser = currentUser();
?>
<!doctype html><html lang="vi"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?> - Nhịp Khoa</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/admin.css"></head><body class="admin-body">
<div class="admin-layout"><aside class="admin-sidebar">
<a class="admin-brand" href="<?= e(BASE_URL) ?>admin/dashboard.php"><img src="<?= e(BASE_URL) ?>assets/images/figma/brand-logo-white.png" alt="Nhịp Khoa"></a>
<p class="admin-label">KHU VỰC QUẢN TRỊ</p>
<nav class="admin-nav" aria-label="Điều hướng quản trị">
<a class="<?= $adminPage === 'dashboard' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>admin/dashboard.php"><span class="admin-nav-icon"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg></span>Tổng quan</a>
<a class="<?= $adminPage === 'users' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>admin/users.php"><span class="admin-nav-icon"><svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3.5 19v-1.5A4.5 4.5 0 0 1 8 13h2a4.5 4.5 0 0 1 4.5 4.5V19"/><path d="M16 5.5a3 3 0 0 1 0 5.5M17 14a4 4 0 0 1 3.5 4v1"/></svg></span>Tài khoản</a>
<a href="<?= e(BASE_URL) ?>editor/posts.php"><span class="admin-nav-icon"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5M9 12h7M9 16h7"/></svg></span>Bài viết</a>
<a class="<?= $adminPage === 'categories' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>views/categories.php">
    <span class="admin-nav-icon">
        <svg aria-hidden="true" viewBox="0 0 24 24">
            <path d="M3 7h7l2 2h9v11H3z"/>
            <path d="M3 7V5h7l2 2"/>
        </svg>
    </span>
    Chuyên mục
</a>
<a class="<?= $adminPage === 'comments' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>admin/binhluan.php">
    <span class="admin-nav-icon">
        <svg aria-hidden="true" viewBox="0 0 24 24">
            <path d="M20 15a3 3 0 0 1-3 3H9l-5 3v-3a3 3 0 0 1-2-3V7a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3z"/>
            <path d="M7 9h10M7 13h7"/>
        </svg>
    </span>
    Bình luận
</a>
</nav><div class="admin-sidebar-note"><strong>Nhịp Khoa</strong><span>Quản lý nội dung an toàn, rõ ràng và đúng vai trò.</span></div>
</aside><div class="admin-workspace"><header class="admin-topbar">
<div><span class="admin-kicker">ADMIN PANEL</span><strong><?= e($pageTitle) ?></strong></div>
<div class="admin-profile"><span class="admin-avatar"><?= e(mb_strtoupper(mb_substr((string) ($adminUser['full_name'] ?? 'A'), 0, 1))) ?></span><span class="admin-profile-copy"><b><?= e($adminUser['full_name'] ?? 'Admin') ?></b><small>Quản trị viên</small></span>
<form method="post" action="<?= BASE_URL ?>dang-xuat.php"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><button type="submit">Đăng xuất</button></form></div>
</header><main class="admin-main">
