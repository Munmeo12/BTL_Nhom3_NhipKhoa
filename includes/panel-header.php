<?php
declare(strict_types=1);
/**
 * Khung giao diện dùng chung cho Admin, Biên tập viên và Tác giả:
 * menu dọc bên trái + thanh trên cùng + vùng nội dung.
 * (Độc giả dùng layout công khai, không có menu trái.)
 *
 * Biến đầu vào (tùy chọn): $pageTitle, $pageCss, $adminPage (tên mục menu đang chọn).
 */
$pageTitle = $pageTitle ?? 'Quản trị';
$pageCss = $pageCss ?? '';
$panelUser = currentUser();
$panelRole = (string) ($panelUser['role'] ?? 'admin');
if (!in_array($panelRole, ['admin', 'editor', 'author'], true)) {
    $panelRole = 'admin';
}

$panelMeta = [
    'admin'  => ['label' => 'KHU VỰC QUẢN TRỊ', 'kicker' => 'ADMIN PANEL', 'name' => 'Quản trị viên', 'aria' => 'Điều hướng quản trị', 'home' => 'admin/dashboard.php'],
    'editor' => ['label' => 'KHU VỰC BIÊN TẬP', 'kicker' => 'EDITOR PANEL', 'name' => 'Biên tập viên', 'aria' => 'Điều hướng biên tập viên', 'home' => 'editor/dashboard.php'],
    'author' => ['label' => 'KHU VỰC TÁC GIẢ', 'kicker' => 'AUTHOR PANEL', 'name' => 'Tác giả', 'aria' => 'Điều hướng tác giả', 'home' => 'author/dashboard.php'],
][$panelRole];

// Mục menu đang chọn: ưu tiên $adminPage do controller truyền, nếu không thì suy ra từ tên file.
$panelCurrent = (string) ($adminPage ?? '');
if ($panelCurrent === '' && $panelRole !== 'admin') {
    $panelFile = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $panelCurrent = match ($panelFile) {
        'dashboard.php' => 'dashboard',
        'posts.php', 'view.php', 'edit.php' => 'posts',
        'create.php' => 'create',
        default => '',
    };
}

$panelIcons = [
    'home'      => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
    'dashboard' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
    'users'     => '<circle cx="9" cy="8" r="3"/><path d="M3.5 19v-1.5A4.5 4.5 0 0 1 8 13h2a4.5 4.5 0 0 1 4.5 4.5V19"/><path d="M16 5.5a3 3 0 0 1 0 5.5M17 14a4 4 0 0 1 3.5 4v1"/>',
    'pen'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
    'post'      => '<path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5M9 12h7M9 16h7"/>',
    'review'    => '<path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5"/><path d="m9 15 2 2 4-4"/>',
    'plus'      => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
    'folder'    => '<path d="M3 7h7l2 2h9v11H3z"/><path d="M3 7V5h7l2 2"/>',
    'tag'       => '<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.2"/>',
    'comment'   => '<path d="M20 15a3 3 0 0 1-3 3H9l-5 3v-3a3 3 0 0 1-2-3V7a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3z"/><path d="M7 9h10M7 13h7"/>',
];

// [khóa, nhãn, đường dẫn, icon]
$panelMenus = [
    'admin' => [
        ['dashboard', 'Tổng quan', 'admin/dashboard.php', 'dashboard'],
        ['users', 'Tài khoản', 'admin/users.php', 'users'],
        ['author-requests', 'Yêu cầu làm tác giả', 'admin/yeu-cau-tac-gia.php', 'pen'],
        ['posts', 'Bài viết', 'editor/posts.php', 'post'],
        ['categories', 'Chuyên mục', 'views/categories.php', 'folder'],
        ['tags', 'Thẻ', 'admin/the.php', 'tag'],
        ['comments', 'Bình luận', 'admin/binhluan.php', 'comment'],
        ['announcements', 'Thông báo chạy', 'admin/thong-bao.php', 'comment'],
    ],
    'editor' => [
        ['dashboard', 'Tổng quan', 'editor/dashboard.php', 'dashboard'],
        ['posts', 'Duyệt bài', 'editor/posts.php', 'review'],
    ],
    'author' => [
        ['dashboard', 'Tổng quan', 'author/dashboard.php', 'dashboard'],
        ['posts', 'Bài viết của tôi', 'author/posts.php', 'post'],
        ['create', 'Tạo bài viết', 'author/create.php', 'plus'],
    ],
][$panelRole];

$panelIcon = static fn(string $key): string =>
    '<span class="admin-nav-icon"><svg aria-hidden="true" viewBox="0 0 24 24">' . $panelIcons[$key] . '</svg></span>';
$panelDisplayName = (string) ($panelUser['full_name'] ?? $panelMeta['name']);
?>
<!doctype html><html lang="vi"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?> - Nhịp Khoa</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<?php if ($panelRole !== 'admin'): ?><link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/common.css"><?php endif; ?>
<link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/admin.css">
<?php if ($pageCss !== ''): ?><link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/<?= e($pageCss) ?>"><?php endif; ?>
</head><body class="admin-body panel-<?= e($panelRole) ?>">
<div class="admin-layout"><aside class="admin-sidebar">
<a class="admin-brand" href="<?= e(BASE_URL . $panelMeta['home']) ?>"><img src="<?= e(BASE_URL) ?>assets/images/figma/brand-logo-white.png" alt="Nhịp Khoa"></a>
<p class="admin-label"><?= e($panelMeta['label']) ?></p>
<nav class="admin-nav" aria-label="<?= e($panelMeta['aria']) ?>">
<a class="admin-nav-home" href="<?= e(BASE_URL) ?>index.php"><?= $panelIcon('home') ?>Trang chủ</a>
<?php foreach ($panelMenus as [$key, $label, $path, $icon]): ?>
<a class="<?= $panelCurrent === $key ? 'active' : '' ?>"<?= $panelCurrent === $key ? ' aria-current="page"' : '' ?> href="<?= e(BASE_URL . $path) ?>"><?= $panelIcon($icon) ?><?= e($label) ?></a>
<?php endforeach; ?>
</nav><div class="admin-sidebar-note"><strong>Nhịp Khoa</strong><span>Quản lý nội dung an toàn, rõ ràng và đúng vai trò.</span></div>
</aside><div class="admin-workspace"><header class="admin-topbar">
<div><span class="admin-kicker"><?= e($panelMeta['kicker']) ?></span><strong><?= e($pageTitle) ?></strong></div>
<div class="admin-profile"><span class="admin-avatar"><?= e(mb_strtoupper(mb_substr($panelDisplayName, 0, 1))) ?></span><span class="admin-profile-copy"><b><?= e($panelDisplayName) ?></b><small><?= e($panelMeta['name']) ?></small></span>
<form method="post" action="<?= e(BASE_URL) ?>dang-xuat.php"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><button type="submit">Đăng xuất</button></form></div>
</header><main class="admin-main<?= $panelRole === 'admin' ? '' : ' panel-flush' ?>">
