<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

(new App\Controllers\ProfileController(
    static fn(): PDO => db(),
    new App\Services\AuthService(),
    new App\Services\ImageUploadService(__DIR__ . '/assets/uploads/avatars', 2 * 1024 * 1024, 'avatar_'),
    new App\Core\View()
))->show(App\Core\Request::capture());
