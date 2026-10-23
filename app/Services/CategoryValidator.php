<?php
declare(strict_types=1);

namespace App\Services;

use PDOException;

/**
 * Kiểm tra dữ liệu danh mục và dịch lỗi MySQL sang thông báo tiếng Việt.
 *
 * Lớp này cố tình không giữ PDO nào để tests/category-validation.php chạy được
 * mà không cần kết nối MySQL.
 */
final class CategoryValidator
{
    public const STATUSES = ['active', 'hidden'];

    private const NAME_LIMIT = 120;
    private const SLUG_LIMIT = 150;

    private string $message = '';

    public function error(): string
    {
        return $this->message !== '' ? $this->message : 'Không thể cập nhật danh mục. Vui lòng thử lại.';
    }

    public function validate(string $name, string $slug, string $status): bool
    {
        $this->message = '';

        if ($name === '' || $slug === '') {
            $this->message = 'Vui lòng nhập đầy đủ tên danh mục và slug.';
        } elseif (mb_strlen($name) > self::NAME_LIMIT || mb_strlen($slug) > self::SLUG_LIMIT) {
            $this->message = sprintf(
                'Tên tối đa %d ký tự, slug tối đa %d ký tự.',
                self::NAME_LIMIT,
                self::SLUG_LIMIT
            );
        } elseif (!in_array($status, self::STATUSES, true)) {
            $this->message = 'Trạng thái danh mục không hợp lệ.';
        }

        return $this->message === '';
    }

    /**
     * Chạy một thao tác ghi và nuốt PDOException thành thông báo an toàn.
     *
     * Không dùng SQLSTATE 23000 vì mã đó gộp cả trùng khóa (1062) lẫn vướng
     * khóa ngoại (1451); chỉ errno mới phân biệt được hai trường hợp.
     */
    public function write(callable $operation): bool
    {
        try {
            return (bool) $operation();
        } catch (PDOException $exception) {
            $this->message = match ((int) ($exception->errorInfo[1] ?? 0)) {
                1062 => 'Tên danh mục hoặc slug đã tồn tại. Vui lòng chọn giá trị khác.',
                1451 => 'Không thể xóa danh mục đang có bài viết.',
                default => 'Không thể cập nhật danh mục. Vui lòng thử lại.',
            };

            return false;
        }
    }
}
