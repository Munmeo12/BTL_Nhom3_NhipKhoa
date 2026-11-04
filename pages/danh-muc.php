<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
$request = App\Core\Request::capture();
(new App\Controllers\PublicPostController(static fn(): PDO => db(), new App\Core\View()))
    ->category($request, trim((string) $request->query('slug', '')));