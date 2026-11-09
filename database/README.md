# Cơ sở dữ liệu Nhịp Khoa

## Các file

- [`schema.sql`](schema.sql): tạo database `nhip_khoa`: 6 bảng chính và 5 bảng bổ sung.
- [`seed.sql`](seed.sql): thêm vai trò, thành viên, danh mục, bài viết và dữ liệu thử.
- [`erd.md`](erd.md): ERD của nhóm viết bằng Mermaid.

## Danh sách bảng

| Bảng | Mục đích |
| --- | --- |
| `roles` | Danh sách vai trò trong hệ thống |
| `users` | Tài khoản của bốn vai trò: admin, biên tập viên, tác giả, độc giả |
| `categories` | Danh mục bài viết |
| `posts` | Nội dung và trạng thái kiểm duyệt bài viết |
| `comments` | Bình luận và trạng thái duyệt/ẩn |
| `impact_box_items` | Bài viết người dùng đã lưu và ghi chú |
| `author_requests` | Yêu cầu xin nâng quyền Độc giả lên Tác giả |
| `tags` | Thẻ bài viết |
| `post_tags` | Quan hệ nhiều-nhiều giữa bài viết và thẻ |
| `announcements` | Thông báo chạy ở đầu trang do Admin quản lý |
| `user_notifications` | Thông báo một lần gửi tới từng người dùng (đổi vai trò, duyệt/từ chối yêu cầu làm tác giả) |

## Thứ tự chạy

```sql
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```

Chạy `schema.sql` trước vì file này tạo cấu trúc bảng. Chạy `seed.sql` sau vì
dữ liệu con chỉ thêm được khi dữ liệu ở bảng cha đã tồn tại.

## Danh mục cho các trang công khai

Sau khi đã tạo database, quản trị viên chạy migration
`migrations/2026_10_18_public_categories.sql` trên đúng database đang dùng.
Script bổ sung Tin khoa, Học tập, Cơ hội, Sự kiện nếu chưa tồn tại; chạy lại
không nhân đôi và không đổi danh mục/bài cũ. Nếu tên đã tồn tại với slug khác,
kiểm tra thủ công trước khi đổi slug; không tự gán lại bài viết cũ.

Không chạy lại schema/seed trên database VPS có dữ liệu. Tác giả chọn danh mục
mới khi viết bài; chỉ bài đã xuất bản trong danh mục đang hoạt động mới xuất hiện.

Database cũ còn các danh mục `cong-nghe`, `giao-duc`, `doi-song` chạy migration
`migrations/2026_10_20_move_legacy_categories.sql` một lần. Script chuyển bài sang
Tin khoa, Học tập, Cơ hội rồi chỉ xóa danh mục cũ khi không còn bài tham chiếu;
ID bài, bình luận và các mục trong Impact Box được giữ nguyên.

Ảnh upload vẫn là file trên máy chạy PHP. Dùng chung MySQL không đồng bộ file ảnh;
cần chép ảnh tương ứng hoặc thiết lập kho ảnh chung ở đợt triển khai riêng.

## Chức năng nâng cao (lượt xem, bài nổi bật, lịch xuất bản)

Với database đã có sẵn, chạy một lần `migrations/2026_11_03_advanced_features.sql`
(thêm `posts.view_count`, `posts.is_featured`). Database tạo mới từ `schema.sql` đã có sẵn hai cột này.
Lịch xuất bản dùng cột `published_at`: bài `published` nhưng `published_at` còn ở tương lai thì chưa hiển thị cho khách.

## Ảnh đại diện và thông báo người dùng

- `users.avatar` (VARCHAR 255, cho phép NULL) lưu tên file ảnh trong `assets/uploads/avatars/`. Database tạo mới từ
  `schema.sql` đã có cột này; database cũ chạy một lần `migrations/2026_11_07_user_avatar.sql`.
- Bảng `user_notifications` lưu thông báo một lần cho từng người dùng (đổi vai trò, duyệt/từ chối yêu cầu làm tác giả).
  Database tạo mới đã có sẵn bảng; database cũ chạy một lần `migrations/2026_11_07_user_notifications.sql`.
  Nếu chưa có bảng này, chức năng chính vẫn chạy nhưng người dùng không nhận được thông báo.