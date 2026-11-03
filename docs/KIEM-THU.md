# Đối chiếu đề bài (Chủ đề 3) và bộ kiểm thử tối thiểu

Tài khoản mẫu (`database/seed.sql`, `seed_demo.sql`), mật khẩu chung `Admin@123`:

| Email | Vai trò | Thành viên |
|---|---|---|
| admin@nhipkhoa.vn | Admin | Trần Thị Hà Anh |
| van@nhipkhoa.vn | Biên tập viên | Nguyễn Hải Vân |
| trung@nhipkhoa.vn | Tác giả A | Đào Khánh Trung |
| tacgia.b@nhipkhoa.vn | Tác giả B (`seed_demo.sql`) | dùng cho tình huống 2 |
| hoc@nhipkhoa.vn | Độc giả | Nguyễn Trung Vũ Học |
| docgia.demo@nhipkhoa.vn | Độc giả (`seed_demo.sql`) | dùng để gửi bình luận |

## Chức năng bắt buộc

| Yêu cầu | Nơi thực hiện |
|---|---|
| CRUD chuyên mục | `admin/` → Chuyên mục (`CategoryController`) |
| CRUD bài viết | Tác giả tạo/sửa/xóa bài của mình (`AuthorPostController`); Admin gỡ/xóa bài (`EditorPostController`) |
| CRUD thẻ | `admin/the.php` (`AdminTagController`, model `Tag`); gắn thẻ khi tạo/sửa bài |
| Workflow nháp → chờ duyệt → đăng/từ chối | cột `posts.status`; tác giả gửi duyệt, biên tập/admin duyệt/từ chối (`Post::review`) |
| Trang chủ, danh mục, chi tiết, tìm kiếm | `index.php`, `pages/*.php`, `bai-viet.php`, `pages/tim-kiem.php` (tìm cả theo thẻ: `?tag=slug`) |
| Bình luận chờ duyệt/hiển thị/ẩn | `comments.status` = pending/approved/hidden; Admin: `admin/binhluan.php` |
| Upload ảnh đại diện kiểm tra loại và kích thước | `ImageUploadService` (JPG/PNG/WEBP, tối đa 5MB, kiểm tra MIME thật) |
| Endpoint JSON bằng Fetch API | `admin/api/duyet-binh-luan.php` (duyệt/ẩn/hiện/xóa bình luận, gọi bằng `fetch()`) |

## Chức năng nâng cao đã có

- RSS (`rss.php`).
- Lượt xem: `posts.view_count`, chỉ đếm độc giả/khách, mỗi phiên 1 lần cho mỗi bài (`Post::incrementViews`).
- Bài nổi bật: `posts.is_featured`, biên tập viên/admin bật tắt; bài nổi bật xếp đầu trang chủ.
- Lịch xuất bản: biên tập viên hẹn giờ khi duyệt, lưu vào `posts.published_at`; chưa tới giờ thì khách chưa thấy.
- Chưa làm: trình soạn thảo rich text.

Với database cũ chạy thêm `database/migrations/2026_11_03_advanced_features.sql`.

## 8 tình huống kiểm thử

| # | Tình huống | Cách thử | Kết quả mong đợi |
|---|---|---|---|
| 1 | Khách không xem được bài nháp qua URL | Mở `bai-viet.php?id=<id bài nháp>` khi chưa đăng nhập | Báo không tìm thấy (404), vì chỉ `published` mới hiện |
| 2 | Tác giả A không sửa bài của tác giả B | Đăng nhập A, mở `author/edit.php?id=<bài của B>` | 404 "không có quyền chỉnh sửa" (`findOwned` lọc theo `author_id`) |
| 3 | Biên tập duyệt bài, bài hiện ngoài trang chủ | Tác giả gửi duyệt → biên tập duyệt → mở trang chủ | Bài xuất hiện |
| 4 | Slug trùng được xử lý | Tạo hai bài cùng tiêu đề | Slug thứ hai có hậu tố `-2` (`Slugger::unique`), không lỗi |
| 5 | Upload file không phải ảnh bị từ chối | Chọn file `.txt`/`.php` làm ảnh đại diện | Thông báo "Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP" |
| 6 | Bình luận chứa script không chạy | Bình luận `<script>alert(1)</script>`, admin duyệt | Hiển thị thành chữ, không hiện hộp thoại (`e()`) |
| 7 | Tìm kiếm chỉ trả bài đã xuất bản | Tìm từ có trong bài nháp | Không thấy bài nháp |
| 8 | CSRF sai thì thao tác bị từ chối | Xóa `csrf_token` trong form bằng DevTools rồi gửi | HTTP 419 "Phiên làm việc không hợp lệ" |

Kịch bản demo: tác giả tạo bài nháp → gửi duyệt → biên tập duyệt → khách tìm và đọc bài →
gửi bình luận → admin duyệt bình luận.

Quy tắc nghiệp vụ: tài khoản bị khóa mất quyền ngay ở request kế tiếp (`AuthService::user()`
đối chiếu lại CSDL), nên tác giả bị khóa không thể sửa/xóa bài nữa.

Gỡ bài: Admin gỡ bài đã đăng thì bài chuyển về `rejected` và lý do được ghi dạng `[Gỡ bài] ...` để tác giả
phân biệt với bài bị từ chối lúc duyệt. Đề không yêu cầu trạng thái "gỡ" riêng nên không thêm trạng thái mới.

Hẹn giờ: bài đã duyệt nhưng `published_at` ở tương lai hiển thị là "Hẹn đăng" cho tác giả và biên tập viên,
khách chưa thấy cho tới khi tới giờ.

Người phụ trách từng tình huống và phạm vi file của từng thành viên: [`PHAN-CONG.md`](PHAN-CONG.md).
