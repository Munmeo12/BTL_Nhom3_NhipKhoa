<?php
$pageTitle = $pageTitle ?? 'Nhịp Khoa';
$activeNav = $activeNav ?? '';
$publicStyles = $publicStyles ?? false;
require dirname(__DIR__, 3) . '/includes/header.php';
echo $content;
require dirname(__DIR__, 3) . '/includes/footer.php';
