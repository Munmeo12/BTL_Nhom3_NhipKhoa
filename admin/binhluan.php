<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

(new App\Controllers\CommentController(
    static fn(): PDO => db(),
    new App\Services\AuthService(),
    new App\Core\View()
))->index(App\Core\Request::capture());
