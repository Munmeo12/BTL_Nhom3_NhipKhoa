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

    private const MEMBERS = [
        ['mssv' => '224001812', 'name' => 'Khổng Thị Lý'],
        ['mssv' => '224001828', 'name' => 'Trần Hà Như Quỳnh'],
        ['mssv' => '224001819', 'name' => 'Trần Nguyễn Bình Nguyên'],
        ['mssv' => '224001843', 'name' => 'Đặng Ánh Tuyết'],
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
