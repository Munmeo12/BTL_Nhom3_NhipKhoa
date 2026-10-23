<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

(new App\Controllers\HomeController(
    static fn(): PDO => db(),
    new App\Core\View()
))->index();
