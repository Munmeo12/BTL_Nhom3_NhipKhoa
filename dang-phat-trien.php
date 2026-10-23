<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

(new App\Controllers\PublicPageController(new App\Core\View()))->placeholder(App\Core\Request::capture());
