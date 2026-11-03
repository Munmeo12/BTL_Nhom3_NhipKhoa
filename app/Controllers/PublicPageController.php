<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

/**
 * Các trang tĩnh không cần chạm tới database.
 */
final class PublicPageController extends Controller
{
    /** Tính năng đang chờ branch khác tích hợp; liên kết chờ trỏ về đây. */
    private const PENDING_FEATURES = [
        'thay-doi' => 'Chi tiết thay đổi đáng chú ý',
        'danh-sach-bai' => 'Danh sách bài viết công khai',
        'bai-minh-hoa' => 'Bài viết minh họa',
        'tin-khoa' => 'Tin khoa',
        'hoc-tap' => 'Học tập',
        'co-hoi' => 'Cơ hội',
        'su-kien' => 'Sự kiện',
        'huong-dan' => 'Hướng dẫn',
        'impact-box' => 'Impact Box',
        'faq' => 'Câu hỏi thường gặp',
        'lien-he' => 'Liên hệ',
        'gop-y' => 'Góp ý',
    ];

    // Nếu giảng viên yêu cầu mã sinh viên, điền vào khóa 'mssv'; cột chỉ hiện khi có dữ liệu.
    private const MEMBERS = [
        ['mssv' => '', 'name' => 'Trần Thị Hà Anh', 'role' => 'Nhóm trưởng - Admin'],
        ['mssv' => '', 'name' => 'Nguyễn Hải Vân', 'role' => 'Biên tập viên'],
        ['mssv' => '', 'name' => 'Đào Khánh Trung', 'role' => 'Tác giả'],
        ['mssv' => '', 'name' => 'Nguyễn Trung Vũ Học', 'role' => 'Độc giả'],
    ];

    public function about(): void
    {
        $this->render('public.about', ['members' => self::MEMBERS]);
    }

    public function placeholder(Request $request): void
    {
        $key = (string) $request->query('feature', 'tinh-nang');

        $this->render('public.placeholder', [
            'feature' => self::PENDING_FEATURES[$key] ?? 'Tính năng này',
            'pageTitle' => 'Đang phát triển',
        ], 'layouts.public');
    }
}
