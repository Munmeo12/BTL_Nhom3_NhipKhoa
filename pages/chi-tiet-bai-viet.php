<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
$request = App\Core\Request::capture();
$query = array_filter($request->allQuery(), 'is_scalar');
App\Core\Response::redirect(BASE_URL . 'bai-viet.php?' . http_build_query($query));
