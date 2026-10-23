<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
requireRole(['editor', 'admin']);

$pdo = db();
$signedInUser = currentUser();
$editorId = (int) $signedInUser['id'];
