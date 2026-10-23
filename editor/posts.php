<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
(new App\Controllers\EditorPostController(
    static fn(): PDO => db(), new App\Services\AuthService(), new App\Core\View()
))->posts(App\Core\Request::capture());

