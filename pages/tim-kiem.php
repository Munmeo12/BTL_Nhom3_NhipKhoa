<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
(new App\Controllers\PublicPostController(static fn(): PDO => db(), new App\Core\View()))
    ->search(App\Core\Request::capture());
