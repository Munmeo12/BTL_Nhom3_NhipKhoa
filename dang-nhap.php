<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

(new App\Controllers\AuthController(
    static fn(): App\Models\User => new App\Models\User(db()),
    new App\Services\AuthService(),
    new App\Core\View()
))->login(App\Core\Request::capture());
