<?php
declare(strict_types=1);

// Bật hiển thị lỗi chi tiết để kiểm tra nguyên nhân
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/bootstrap/app.php';

(new App\Controllers\HomeController(
    static fn(): PDO => db(),
    new App\Core\View()
))->index();