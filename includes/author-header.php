<?php
$pageTitle = $pageTitle ?? 'Khu vực tác giả';
$pageCss = $pageCss ?? '';
$currentPage = basename($_SERVER['PHP_SELF']);
$displayName = currentUser()['full_name'] ?? 'Tác giả';
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($pageTitle) ?> - Nhịp Khoa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@600;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>assets/css/common.css">

    <?php if ($pageCss !== ''): ?>
        <link rel="stylesheet"
              href="<?= BASE_URL ?>assets/css/<?= e($pageCss) ?>">
    <?php endif; ?>
</head>

<body>

<header class="site-header">
    <div class="header-container">

        <a href="<?= BASE_URL ?>index.php">
            <img src="<?= BASE_URL ?>assets/images/logo.svg"
                 alt="Nhịp Khoa"
                 class="logo-image">
        </a>

        <nav class="main-menu">

            <a href="<?= BASE_URL ?>author/dashboard.php"
               class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                Tổng quan
            </a>

            <a href="<?= BASE_URL ?>author/posts.php"
               class="<?= in_array($currentPage, ['posts.php', 'view.php', 'edit.php']) ? 'active' : '' ?>">
                Bài viết của tôi
            </a>

            <a href="<?= BASE_URL ?>author/create.php"
               class="<?= $currentPage === 'create.php' ? 'active' : '' ?>">
                Tạo bài viết
            </a>

        </nav>

        <div class="user-info">

            <i class="fa-regular fa-user"></i>

            <span><?= e($displayName) ?></span>

            <form method="post"
                  action="<?= BASE_URL ?>dang-xuat.php"
                  class="logout-form">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= e(csrfToken()) ?>">

                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Đăng xuất
                </button>

            </form>

        </div>

    </div>
</header>

<main class="main-content">