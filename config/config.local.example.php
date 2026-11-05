<?php
declare(strict_types=1);

// Sao chép file này thành config.local.php rồi sửa theo MySQL trên máy của bạn.
// config.local.php đã được Git bỏ qua nên mật khẩu và tên CSDL riêng không bị commit.
return [
    'host' => 'localhost',
    'port' => '3306',
    'name' => 'nhip_khoa',
    'user' => 'root',
    'password' => '',
    // Khi kết nối MySQL trên VPS, bỏ chú thích dòng dưới.
    // 'ssl_ca' => __DIR__ . '/certs/mysql-ca.pem',
    // Đặt '1' nếu chứng chỉ máy chủ khớp tên host để xác minh server MySQL (mặc định '0').
    // 'ssl_verify' => '0',
];
