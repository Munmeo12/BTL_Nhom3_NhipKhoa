<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class ImageUploadService
{
    private const TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(
        private readonly string $directory,
        private readonly int $maxBytes = 5 * 1024 * 1024
    ) {
    }

    public function store(?array $file): ?string
    {
        if ($file === null || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Có lỗi khi tải ảnh lên.');
        }
        if ((int) ($file['size'] ?? 0) > $this->maxBytes) {
            throw new RuntimeException('Ảnh không được lớn hơn 5MB.');
        }
        $temporary = (string) ($file['tmp_name'] ?? '');
        $mime = $temporary !== '' ? mime_content_type($temporary) : false;
        if (!is_string($mime) || !isset(self::TYPES[$mime])) {
            throw new RuntimeException('Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.');
        }
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Không thể tạo thư mục lưu ảnh.');
        }
        $name = 'post_' . bin2hex(random_bytes(12)) . '.' . self::TYPES[$mime];
        if (!move_uploaded_file($temporary, $this->directory . DIRECTORY_SEPARATOR . $name)) {
            throw new RuntimeException('Không thể lưu ảnh.');
        }
        return $name;
    }

    public function delete(?string $name): void
    {
        $name = basename((string) $name);
        if ($name === '') {
            return;
        }
        $path = $this->directory . DIRECTORY_SEPARATOR . $name;
        if (is_file($path)) {
            unlink($path);
        }
    }
}
