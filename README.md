# Nhịp Khoa - Website tin tức/blog có trang quản trị

Bài tập lớn môn Lập trình web, **Chủ đề 3**: cổng tin tức nhỏ cho khoa, nơi tác giả viết bài,
biên tập viên duyệt bài, khách đọc và bình luận, quản trị viên quản lý toàn bộ nội dung.

## Nhóm thực hiện: nhóm 3

| Họ và tên | MSSV | Vai trò trong nhóm | Vai trò hệ thống phụ trách |
|---|---|---|
| Trần Thị Hà Anh | 224001713 | Nhóm trưởng | **Admin** + nền tảng (đăng nhập, phân quyền, tài khoản, chuyên mục, thẻ) |
| Nguyễn Hải Vân | 224001759 | Thành viên | **Biên tập viên** (duyệt bài, kiểm duyệt bình luận) |
| Đào Khánh Trung | 224001757 | Thành viên | **Tác giả** (viết bài, upload ảnh, slug, yêu cầu làm tác giả) |
| Nguyễn Trung Vũ Học | 224001719 | Thành viên | **Độc giả/Khách** (trang công khai, tìm kiếm, bình luận, Impact Box) |

Phạm vi file của từng người: [`docs/PHAN-CONG.md`](docs/PHAN-CONG.md).

## Chức năng đã hoàn thành

- **Khách:** xem trang chủ, danh mục, chi tiết bài, tìm kiếm (theo nội dung, tác giả, chuyên mục, thẻ), lọc theo thẻ, RSS.
- **Độc giả:** đăng ký (mật khẩu có chữ hoa, chữ thường, số, ký tự đặc biệt), đăng nhập, bình luận (chờ duyệt),
  lưu bài vào Impact Box kèm ghi chú, gửi yêu cầu làm tác giả.
- **Tác giả:** tạo, sửa, xóa bài của mình, lưu nháp hoặc gửi duyệt, chọn thẻ, upload ảnh đại diện
  (JPG/PNG/WEBP, tối đa 5MB, kiểm tra MIME thật), xem lý do khi bị từ chối hoặc bị gỡ.
- **Biên tập viên:** duyệt, từ chối (bắt buộc ghi lý do), hẹn giờ xuất bản, đánh dấu bài nổi bật.
- **Admin:** quản lý tài khoản (đổi vai trò, khóa/mở khóa), chuyên mục, thẻ, yêu cầu làm tác giả; gỡ hoặc xóa bài;
  kiểm duyệt bình luận (duyệt/ẩn/hiện/xóa) bằng endpoint JSON gọi qua `fetch()`.
- **Nâng cao:** RSS, lượt xem, bài nổi bật, lịch xuất bản. Chưa làm: trình soạn thảo rich text.

Luồng bài viết: `nháp → chờ duyệt → đã đăng` hoặc `từ chối` (kèm lý do). Chỉ bài đã đăng và đã tới giờ xuất bản mới hiện với khách.

## Công nghệ

HTML, CSS, JavaScript (Fetch API), PHP 8 thuần theo mô hình MVC, MySQL (PDO prepared statement), chạy trên XAMPP.

## Cách chạy trên máy

1. Cài XAMPP, đặt thư mục project vào `C:\xampp\htdocs\` (ví dụ `btl_laptrinhweb`), bật **Apache** và **MySQL**.
2. Tạo cấu hình kết nối: sao chép `config/config.local.example.php` thành `config/config.local.php`,
   rồi sửa `name`, `user`, `password`, `host`, `port`.
   Lưu ý cổng mặc định trong file mẫu là **3307**; XAMPP thường dùng **3306**, hãy đổi nếu kết nối lỗi.
3. Tạo CSDL (mở MySQL Command Line tại thư mục project):

   ```sql
   SOURCE database/schema.sql;
   SOURCE database/seed.sql;
   SOURCE database/seed_demo.sql;   -- tùy chọn: thêm tác giả B và độc giả mẫu để thử kiểm thử
   ```

   `schema.sql` chạy trước, `seed.sql` chạy sau. Cả hai dùng `utf8mb4` nên lưu được tiếng Việt.
4. Mở `http://localhost/btl_laptrinhweb/`.

### Nâng cấp database đã có (không chạy lại schema/seed)

Chạy theo thứ tự, chỉ chạy file nào database của bạn chưa có:

| File trong `database/migrations/` | Nội dung |
|---|---|
| `2026_09_06_public_categories.sql` | Thêm 4 chuyên mục công khai (Tin khoa, Học tập, Cơ hội, Sự kiện) |
| `2026_09_06_role_content.sql` | Dữ liệu cho khu vực Tác giả và Biên tập viên |
| `2026_09_08_add_category_description_created_at.sql` | Thêm cột mô tả, ngày tạo cho chuyên mục |
| `2026_09_08_move_legacy_categories.sql` | Chuyển bài từ chuyên mục cũ sang chuyên mục mới |
| `2026_10_23_add_author_requests.sql` | Bảng yêu cầu làm tác giả |
| `2026_10_23_add_tags.sql` | Bảng thẻ (`tags`, `post_tags`) |
| `2026_11_03_advanced_features.sql` | Cột `view_count`, `is_featured` (lượt xem, bài nổi bật) |
| `2026_11_03_tags_demo.sql` | Thẻ mẫu (tùy chọn) |

Biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_SSL_CA`, `DB_SSL_VERIFY`
(nếu có) được ưu tiên hơn `config.local.php`.

### Dùng chung MySQL trên VPS (không bắt buộc)

Tạo `config/config.local.php` với địa chỉ, tài khoản và mật khẩu do nhóm trưởng chia sẻ riêng, thêm
`'ssl_ca' => __DIR__ . '/certs/mysql-ca.pem'`. Không ghi địa chỉ hay mật khẩu thật vào README hoặc commit lên Git.
Ảnh upload nằm trên máy chạy PHP nên không tự đồng bộ giữa các máy dù dùng chung MySQL.

## Tài khoản mẫu

Mật khẩu chung cho mọi tài khoản mẫu: `Admin@123`.

| Email | Vai trò | Thành viên |
|---|---|---|
| `admin@nhipkhoa.vn` | Admin | Trần Thị Hà Anh |
| `van@nhipkhoa.vn` | Biên tập viên | Nguyễn Hải Vân |
| `trung@nhipkhoa.vn` | Tác giả | Đào Khánh Trung |
| `hoc@nhipkhoa.vn` | Độc giả | Nguyễn Trung Vũ Học |
| `tacgia.b@nhipkhoa.vn`, `docgia.demo@nhipkhoa.vn` | Tác giả B, độc giả (trong `seed_demo.sql`) | dùng cho tình huống kiểm thử |
| `khoa.locked@nhipkhoa.vn`, `lan.locked@nhipkhoa.vn` | Tài khoản bị khóa | thử cảnh báo đăng nhập |

## Cấu trúc thư mục

Toàn bộ logic nằm trong `app/` (MVC). Các file `.php` ở ngoài chỉ là **điểm vào mỏng**, giữ nguyên URL.

```text
BTL_NhipKhoa/
├── index.php, bai-viet.php, dang-nhap.php, dang-ky.php, rss.php, ...   ← điểm vào công khai
├── admin/ author/ editor/ pages/ views/                                ← điểm vào theo khu vực
├── bootstrap/app.php     session, BASE_URL, autoload, e()/csrfToken()/verifyCsrf()
├── app/
│   ├── Core/             Controller, Request, Response, View
│   ├── Controllers/      Auth, AdminUser, Category, AdminTag, AuthorPost, EditorPost,
│   │                     Comment, AuthorRequest, Home, PublicPost, PublicArticle, PublicPage, ImpactBox
│   ├── Models/           User, Post, Category, Tag, Comment, ImpactBox, AuthorRequest
│   ├── Services/         AuthService, ImageUploadService, Slugger, CategoryValidator
│   └── Views/            layouts/ (admin, auth, author, editor, public) và giao diện từng khu vực
├── includes/             khung dùng chung (xem includes/README.md)
├── assets/               css/ js/ images/ uploads/
├── config/               database.php, config.local.example.php, certs/
├── database/             schema.sql, seed.sql, seed_demo.sql, migrations/, erd.md
├── docs/  tests/
```

Hai thư mục dễ nhầm:

- **`views/`** (chữ thường, ở gốc) chỉ chứa file điểm vào cho chuyên mục và Impact Box. Giao diện thật nằm ở **`app/Views/`**.
  Xem [`views/README.md`](views/README.md).
- **`pages/chi-tiet-bai-viet.php`** và **`pages/chi-tiet-thay-doi.php`** chỉ chuyển hướng cho link cũ.

### Quy ước điểm vào

Mỗi URL là một file PHP thật (Apache/XAMPP không có router). File chỉ nạp bootstrap và gọi một action:

```php
require_once __DIR__ . '/../bootstrap/app.php';

(new App\Controllers\AdminUserController(
    static fn(): PDO => db(),
    new App\Services\AuthService(),
    new App\Core\View()
))->users(App\Core\Request::capture());
```

Thêm chức năng mới: viết Model trong `app/Models`, Controller trong `app/Controllers`, View trong
`app/Views`, rồi tạo file điểm vào. Không viết truy vấn hay xử lý form trực tiếp trong file điểm vào.

## Bảo mật

- Mật khẩu lưu bằng `password_hash`, kiểm tra bằng `password_verify`; session đổi id sau khi đăng nhập.
- Mọi form POST kiểm tra CSRF (`verifyCsrf()`); dữ liệu in ra qua `e()` chống XSS (kể cả bình luận).
- Mọi truy vấn dùng PDO prepared statement.
- Phân quyền kiểm tra ở server bằng `requireRole([...])`. Khóa tài khoản hoặc đổi vai trò có hiệu lực ngay
  (`AuthService::user()` đối chiếu lại CSDL ở mỗi request).
- Upload ảnh kiểm tra MIME thật, giới hạn 5MB, đặt tên ngẫu nhiên.
- Slug bài viết và thẻ sinh từ tiêu đề, trùng thì thêm hậu tố `-2`, `-3`.
- Kết nối SSL tới MySQL VPS mặc định không xác minh chứng chỉ máy chủ; đặt `ssl_verify` = `'1'` nếu chứng chỉ khớp host.

## Kiểm thử

Bộ 8 tình huống kiểm thử tối thiểu và đối chiếu với đề: [`docs/KIEM-THU.md`](docs/KIEM-THU.md).
Script tự động và cách chạy: [`tests/README.md`](tests/README.md).

## Giới hạn đã biết

- Trang danh mục công khai chỉ có 4 chuyên mục cố định (`tin-khoa`, `hoc-tap`, `co-hoi`, `su-kien`).
- **Gỡ bài** (Admin) đưa bài về trạng thái `rejected` và ghi `[Gỡ bài]` vào lý do, vì đề không yêu cầu thêm trạng thái mới.
- Bài hẹn giờ chưa tới giờ hiển thị là "Hẹn đăng" với tác giả và biên tập viên, khách chưa thấy.
- Chưa có trình soạn thảo rich text; chưa giới hạn số lần đăng nhập sai.
- Một số trang phụ (Hướng dẫn, FAQ, Liên hệ, Góp ý) dẫn tới trang "Đang phát triển".

## Tài liệu

- [`docs/PHAN-CONG.md`](docs/PHAN-CONG.md): phân công và bản đồ code theo vai trò.
- [`docs/KIEM-THU.md`](docs/KIEM-THU.md): đối chiếu đề bài và kiểm thử.
- [`docs/NGUOI-1-FLOW.md`](docs/NGUOI-1-FLOW.md): luồng đăng ký, đăng nhập, phân quyền, quản lý tài khoản.
- [`docs/SYSTEM-STATUS.md`](docs/SYSTEM-STATUS.md): trạng thái hệ thống và checklist trước khi nộp.
- [`docs/GITHUB-FLOW.md`](docs/GITHUB-FLOW.md): quy trình Git của nhóm.
- [`database/README.md`](database/README.md), [`database/erd.md`](database/erd.md): CSDL.
