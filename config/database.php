<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $localFile = __DIR__ . '/config.local.php';
    $local = is_file($localFile) ? require $localFile : [];
    $local = is_array($local) ? $local : [];

    $value = static function (string $environmentName, string $localName, string $default) use ($local): string {
        $environmentValue = getenv($environmentName);
        if ($environmentValue !== false) {
            return $environmentValue;
        }
        return isset($local[$localName]) ? (string) $local[$localName] : $default;
    };

    $host = $value('DB_HOST', 'host', '127.0.0.1');
    // SỬA DÒNG DƯỚI ĐÂY: Đổi '3307' thành '3306'
    $port = $value('DB_PORT', 'port', '3306'); 
    
    $name = $value('DB_NAME', 'name', 'nhip_khoa');
    $user = $value('DB_USER', 'user', 'root');
    $password = $value('DB_PASSWORD', 'password', '');

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $sslCa = $value('DB_SSL_CA', 'ssl_ca', '');
    if ($sslCa !== '') {
        $options[defined('Pdo\Mysql::ATTR_SSL_CA')
            ? Pdo\Mysql::ATTR_SSL_CA
            : PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
        $options[defined('Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT')
            ? Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT
            : PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $value('DB_SSL_VERIFY', 'ssl_verify', '0') === '1';
    }

    // Thêm các dấu ngoặc nhọn {} quanh biến để tránh lỗi cú pháp nếu chuỗi có ký tự đặc biệt
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $password, $options);
    return $pdo;
}