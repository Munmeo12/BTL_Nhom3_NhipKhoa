<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

(new App\Controllers\CategoryController(
    static fn(): PDO => db(),
    new App\Services\AuthService(),
    new App\Core\View()
))->edit(App\Core\Request::capture());
