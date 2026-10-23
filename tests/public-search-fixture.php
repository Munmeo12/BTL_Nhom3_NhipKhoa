<?php
declare(strict_types=1);
// Creates a NEW disposable local database. Never uses config.local.php.
$name = $argv[1] ?? '';
if (!preg_match('/^nhip_khoa_test_[a-z0-9_]+$/', $name)) {
    throw new RuntimeException('Pass a dedicated nhip_khoa_test_* database name.');
}
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$existing = $pdo->prepare('SELECT COUNT(*) FROM information_schema.schemata WHERE schema_name=?');
$existing->execute([$name]);
if ($existing->fetchColumn()) throw new RuntimeException('Refusing to replace an existing database.');
$schema = file_get_contents(__DIR__ . '/../database/schema.sql');
$schema = str_replace(['CREATE DATABASE IF NOT EXISTS nhip_khoa', 'USE nhip_khoa;'], ['CREATE DATABASE ' . $name, 'USE ' . $name . ';'], $schema);
$pdo->exec($schema);
$migration = file_get_contents(__DIR__ . '/../database/migrations/2026_09_06_public_categories.sql');
$pdo->exec($migration);
$pdo->exec($migration);
if ((int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() !== 4) throw new RuntimeException('Migration not idempotent.');
$pdo->exec("INSERT INTO roles (code,name) VALUES ('reader','Reader'),('author','Author'),('editor','Editor'),('admin','Admin')");
$user = $pdo->prepare('INSERT INTO users(role_id,email,password_hash,full_name,status) VALUES(?,?,?,?,?)');
foreach (['reader','author','editor','admin'] as $i => $role) {
    $user->execute([$i + 1, $role . '@example.test', password_hash('TestOnly123!', PASSWORD_DEFAULT), 'Tác giả kiểm thử ' . $role, 'active']);
}
$user->execute([1, 'locked@example.test', password_hash('TestOnly123!', PASSWORD_DEFAULT), 'Locked test', 'locked']);
$categories = $pdo->query('SELECT slug,id FROM categories')->fetchAll(PDO::FETCH_KEY_PAIR);
$pdo->exec("INSERT INTO categories(slug,name,status) VALUES('hidden-test','Hidden','hidden')");
$hiddenId = $pdo->lastInsertId();
$insert = $pdo->prepare('INSERT INTO posts(category_id,author_id,title,slug,summary,thumbnail,content,status,published_at) VALUES(?,2,?,?,?,?,?,?,?)');
$ids = [];
for ($i = 1; $i <= 9; $i++) {
    $insert->execute([$categories['tin-khoa'], "Học bổng tiếng Việt $i", "test-$i", "Thông báo học bổng đặc biệt O'Reilly", $i === 1 ? 'assets/images/figma/home-card-1.png' : 'missing.png', "Nội dung kiểm thử\n<script>window.injected=true</script>", 'published', '2026-09-01 12:00:00']);
    $ids[] = (int) $pdo->lastInsertId();
}
foreach (['hoc-tap','co-hoi','su-kien'] as $slug) {
    $insert->execute([$categories[$slug], "Bài kiểm thử $slug", $slug . '-test', 'Tóm tắt thử nghiệm', null, 'Nội dung bài', 'published', '2026-09-01 12:00:00']);
}
$private = [];
foreach (['draft','pending','rejected'] as $status) {
    $insert->execute([$categories['tin-khoa'], 'PRIVATE-' . $status, 'private-' . $status, 'Private', null, 'Hidden content', $status, null]);
    $private[] = (int) $pdo->lastInsertId();
}
$insert->execute([$hiddenId, 'PRIVATE-category', 'hidden-category', 'Hidden', null, 'Hidden', 'published', '2026-09-01 12:00:00']);
$private[] = (int) $pdo->lastInsertId();
echo json_encode(['database' => $name, 'published' => $ids[0], 'pending' => $private[1], 'private' => $private], JSON_UNESCAPED_UNICODE), PHP_EOL;
