<?php
declare(strict_types=1);
// Explicit opt-in only. Owns uniquely tagged test data, never imports schema/seed.
if (($argv[1] ?? '') !== '--allow-shared-vps') exit("Explicit shared database authorization required\n");
$action = $argv[2] ?? '';
$run = $argv[3] ?? '';
if (!preg_match('/^qa-ly-[a-f0-9]{16}$/', $run)) exit("Invalid run id\n");
require __DIR__ . '/../config/database.php';
$pdo = db();
if ($pdo->query('SELECT @@hostname')->fetchColumn() !== 'vultr-01'
    || $pdo->query('SELECT DATABASE()')->fetchColumn() !== 'nhip_khoa') {
    throw new RuntimeException('Unexpected database; refusing writes.');
}
$file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $run . '.json';
$prefix = '[TEST] ' . $run;
$tables = ['roles','users','categories','posts','comments','impact_box_items'];
function execute(string $sql, array $args = []): PDOStatement {
    global $pdo;
    $s = $pdo->prepare($sql); $s->execute($args); return $s;
}
function fingerprints(): array {
    global $pdo, $tables;
    $result = [];
    foreach ($tables as $table) foreach ($pdo->query("SELECT * FROM $table ORDER BY id") as $row) {
        $result[$table][$row['id']] = hash('sha256', json_encode($row));
    }
    return $result;
}
if ($action === 'setup') {
    if (is_file($file)) throw new RuntimeException('Run already exists');
    $data = ['run'=>$run, 'prefix'=>$prefix, 'before'=>fingerprints(), 'users'=>[], 'posts'=>[], 'categories'=>[]];
    $pdo->beginTransaction();
    try {
        $password = bin2hex(random_bytes(16));
        foreach (['admin','reader','author'] as $role) {
            $roleId = execute('SELECT id FROM roles WHERE code=?', [$role])->fetchColumn();
            if (!$roleId) throw new RuntimeException('Required role missing');
            $email = $run . '-' . $role . '@example.test';
            execute('INSERT INTO users(role_id,email,password_hash,full_name,status) VALUES(?,?,?,?,?)',
                [$roleId,$email,password_hash($password,PASSWORD_DEFAULT),$prefix . ' ' . $role,'active']);
            $data['users'][$role] = ['id'=>(int)$pdo->lastInsertId(),'email'=>$email];
        }
        foreach (['active','hidden'] as $status) {
            execute('INSERT INTO categories(slug,name,status) VALUES(?,?,?)', [$run.'-'.$status,$prefix.' '.$status,$status]);
            $data['categories'][$status] = (int)$pdo->lastInsertId();
        }
        foreach (['published','draft','pending','hidden'] as $type) {
            execute('INSERT INTO posts(category_id,author_id,title,slug,summary,thumbnail,content,status,published_at) VALUES(?,?,?,?,?,?,?,?,?)',
                [$data['categories'][$type==='hidden'?'hidden':'active'],$data['users']['author']['id'],
                 $prefix.' '.$type,$run.'-'.$type,$prefix.' summary',
                 $type==='published'?'assets/images/figma/home-card-1.png':null,
                 "Nội dung thử\n<script>window.qaInjected=1</script>",
                 $type==='hidden'?'published':$type,in_array($type,['published','hidden'],true)?date('Y-m-d H:i:s'):null]);
            $data['posts'][$type] = (int)$pdo->lastInsertId();
        }
        execute('INSERT INTO comments(post_id,user_id,content,status) VALUES(?,?,?,?)',
            [$data['posts']['published'],$data['users']['reader']['id'],$prefix.' comment','pending']);
        $data['comment'] = (int)$pdo->lastInsertId();
        // Journal IDs before committing so cleanup remains possible if the runner stops.
        if (file_put_contents($file,json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))===false) throw new RuntimeException('Cannot journal fixture');
        $pdo->commit();
        unset($data['before']);
        $data['password']=$password;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    exit;
}
if (!is_file($file)) throw new RuntimeException('Missing fixture journal');
$data = json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR);
if ($action === 'state') {
    echo json_encode([
        'categories'=>execute('SELECT id,name,slug,status FROM categories WHERE slug LIKE ?',[$run.'-%'])->fetchAll(),
        'comments'=>execute(
            'SELECT id,status,content FROM comments WHERE post_id=? AND user_id=? AND content LIKE ? ORDER BY id',
            [$data['posts']['published'],$data['users']['reader']['id'],$prefix.'%']
        )->fetchAll(),
        'reader_status'=>execute('SELECT status FROM users WHERE id=? AND email=?',[
            $data['users']['reader']['id'],
            $data['users']['reader']['email'],
        ])->fetchColumn(),
        'saved'=>execute('SELECT user_id,post_id,note FROM impact_box_items WHERE user_id IN (?,?,?)',array_column($data['users'],'id'))->fetchAll()
    ],JSON_UNESCAPED_UNICODE); exit;
}
if ($action === 'hide' || $action === 'unhide') {
    execute('UPDATE categories SET status=? WHERE id=? AND slug=?',[$action==='hide'?'hidden':'active',$data['categories']['active'],$run.'-active']);
    echo '{}'; exit;
}
if ($action === 'lock-reader' || $action === 'unlock-reader') {
    execute('UPDATE users SET status=? WHERE id=? AND email=?',[
        $action === 'lock-reader' ? 'locked' : 'active',
        $data['users']['reader']['id'],
        $data['users']['reader']['email'],
    ]);
    echo '{}'; exit;
}
if ($action !== 'cleanup') throw new RuntimeException('Unsupported action');
$pdo->beginTransaction();
try {
    // Exact IDs plus ownership markers protect existing/team-created rows.
    foreach ($data['users'] as $u) {
        execute('DELETE FROM impact_box_items WHERE user_id=?',[$u['id']]);
    }
    execute(
        'DELETE FROM comments WHERE post_id=? AND user_id=? AND content LIKE ?',
        [$data['posts']['published'],$data['users']['reader']['id'],$prefix.'%']
    );
    foreach($data['posts'] as $type=>$id) {
        // Never cascade-delete another person's comment/save on a test post.
        if (execute('SELECT COUNT(*) FROM comments WHERE post_id=?',[$id])->fetchColumn()
            || execute('SELECT COUNT(*) FROM impact_box_items WHERE post_id=?',[$id])->fetchColumn()) {
            throw new RuntimeException('Foreign interaction on fixture; cleanup requires review');
        }
        execute('DELETE FROM posts WHERE id=? AND author_id=? AND slug=?',[$id,$data['users']['author']['id'],$run.'-'.$type]);
    }
    $cats=execute('SELECT id FROM categories WHERE slug LIKE ? AND name LIKE ?',[$run.'-%',$prefix.'%'])->fetchAll(PDO::FETCH_COLUMN);
    foreach($cats as $id) execute('DELETE FROM categories WHERE id=?',[$id]);
    foreach($data['users'] as $u) {
        if (execute('SELECT COUNT(*) FROM posts WHERE author_id=? OR reviewer_id=?',[$u['id'],$u['id']])->fetchColumn()
            || execute('SELECT COUNT(*) FROM comments WHERE user_id=?',[$u['id']])->fetchColumn()) throw new RuntimeException('Unexpected fixture user data');
        execute('DELETE FROM users WHERE id=? AND email=?',[$u['id'],$u['email']]);
    }
    $pdo->commit();
} catch(Throwable $e) {if($pdo->inTransaction())$pdo->rollBack();throw $e;}
$now=fingerprints();$changed=[];
foreach($data['before'] as $table=>$rows) foreach($rows as $id=>$hash) if(($now[$table][$id]??null)!==$hash) $changed[]=$table.':'.$id;
echo json_encode(['cleanup'=>'complete','existing_rows_changed'=>$changed]);
