<?php 
declare(strict_types=1);

namespace App\Services;

/**
 * Tạo slug từ tiêu đề tiếng Việt và bảo đảm slug không bị trùng.
 */
final class Slugger
{
    private const FROM = 'àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ';
    private const TO = 'aaaaaaaaaaaaaaaaaeeeeeeeeeeeiiiiiooooooooooooooooouuuuuuuuuuuyyyyyd';

    public static function slugify(string $text, int $max = 120): string
    {
        $text = trim($text);
        if (class_exists(\Normalizer::class)) {
            $text = \Normalizer::normalize($text, \Normalizer::FORM_C) ?: $text;
        }
        $text = mb_strtolower($text, 'UTF-8');
        $text = strtr($text, array_combine(mb_str_split(self::FROM), str_split(self::TO)));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim(substr(trim($text, '-'), 0, $max), '-');
    }

    /**
     * Nếu $base đã tồn tại thì thêm hậu tố -2, -3, ... cho tới khi duy nhất.
     *
     * @param callable(string): bool $exists
     */
    public static function unique(string $base, callable $exists, string $fallback = 'bai-viet'): string
    {
        $slug = $base !== '' ? $base : $fallback;
        $candidate = $slug;
        for ($i = 2; $exists($candidate); $i++) {
            $candidate = $slug . '-' . $i;
            if ($i > 500) {
                return $slug . '-' . bin2hex(random_bytes(4));
            }
        }
        return $candidate;
    }
}
