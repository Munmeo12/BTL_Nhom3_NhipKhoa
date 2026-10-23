<?php
declare(strict_types=1);
// Unit checks only: never connects to MySQL or loads config.local.php.
require_once __DIR__ . '/../app/Services/CategoryValidator.php';

use App\Services\CategoryValidator;

$validator = new CategoryValidator();
$writes = 0;
$failure = 0;

/** Mô phỏng luồng CategoryController::form(): validate xong mới ghi. */
$save = static function (string $name, string $slug, string $status) use ($validator, &$writes, &$failure): bool {
    if (!$validator->validate($name, $slug, $status)) {
        return false;
    }

    return $validator->write(static function () use (&$writes, &$failure): bool {
        $writes++;
        if ($failure !== 0) {
            $error = new PDOException('Sensitive SQL must not be displayed');
            $error->errorInfo = ['23000', $failure, 'private diagnostic'];
            throw $error;
        }
        return true;
    });
};

$count = 0;
function check(bool $condition): void
{
    global $count;
    if (!$condition) {
        throw new RuntimeException('Failed check ' . ($count + 1));
    }
    $count++;
}

check($save('Sự kiện', 'su-kien', 'active'));
check($save('Sự kiện', 'su-kien', 'hidden'));

// Trạng thái không hợp lệ phải bị chặn trước khi chạm tới database.
foreach (['', 'pending', 'ACTIVE'] as $status) {
    $before = $writes;
    check(!$save('Sự kiện', 'su-kien', $status));
    check($writes === $before);
}

check(!$save(str_repeat('a', 121), 'test', 'active'));
check(!$save('Test', str_repeat('b', 151), 'active'));
check(!$save('Test', '', 'active'));

$failure = 1062;
check(!$save('Sự kiện', 'other', 'active'));
check(str_contains($validator->error(), 'đã tồn tại'));

// SQLSTATE 23000 gộp cả 1062 lẫn 1451, nên chỉ errno mới tách được hai lỗi.
$failure = 1451;
check(!$save('Sự kiện', 'other', 'active'));
check(str_contains($validator->error(), 'đang có bài viết'));

$failure = 2002;
check(!$save('Test', 'test', 'active'));
check(!str_contains($validator->error(), 'Sensitive'));

echo "Category unit checks passed: $count\n";
