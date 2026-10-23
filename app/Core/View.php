<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public function render(string $template, array $data = [], ?string $layout = null): void
    {
        $content = $this->capture($this->path($template), $data);

        if ($layout === null) {
            echo $content;
            return;
        }

        echo $this->capture($this->path($layout), ['content' => $content] + $data);
    }

    /**
     * Nạp template trong một scope riêng.
     *
     * Biến cục bộ ở đây phải có tiền tố __ vì extract(..., EXTR_SKIP) bỏ qua
     * mọi khóa trùng tên biến đang tồn tại: trước đây tham số $data của
     * render() làm khóa 'data' bị bỏ âm thầm, khiến app/Views/public/list.php
     * nhận $data là cả mảng render thay vì mảng phân trang.
     */
    private function capture(string $__file, array $__data): string
    {
        extract($__data, EXTR_SKIP);

        ob_start();
        require $__file;

        return (string) ob_get_clean();
    }

    private function path(string $name): string
    {
        $path = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $name) . '.php';
        if (!is_file($path)) {
            throw new RuntimeException('View not found: ' . $name);
        }
        return $path;
    }
}
