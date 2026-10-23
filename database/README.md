# Cơ sở dữ liệu Nhịp Khoa

## Các file

- [`schema.sql`](schema.sql): tạo database `nhip_khoa` và 6 bảng chính.
- [`seed.sql`](seed.sql): thêm vai trò, thành viên, danh mục, bài viết và dữ liệu thử.
- [`erd.md`](erd.md): ERD của nhóm viết bằng Mermaid.

## Danh sách bảng

| Bảng | Mục đích |
| --- | --- |
| `roles` | Danh sách vai trò trong hệ thống |
| `users` | Tài khoản quản trị, biên tập viên và tác giả |
| `categories` | Danh mục bài viết |
| `posts` | Nội dung và trạng thái kiểm duyệt bài viết |
| `comments` | Bình luận và trạng thái duyệt/ẩn |
| `impact_box_items` | Bài viết người dùng đã lưu và ghi chú |

## Thứ tự chạy

```sql
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```

Chạy `schema.sql` trước vì file này tạo cấu trúc bảng. Chạy `seed.sql` sau vì
dữ liệu con chỉ thêm được khi dữ liệu ở bảng cha đã tồn tại.

## Danh mục cho các trang công khai

Sau khi đã tạo database, quản trị viên chạy migration
`migrations/2026_09_06_public_categories.sql` trên đúng database đang dùng.
Script bổ sung Tin khoa, Học tập, Cơ hội, Sự kiện nếu chưa tồn tại; chạy lại
không nhân đôi và không đổi danh mục/bài cũ. Nếu tên đã tồn tại với slug khác,
kiểm tra thủ công trước khi đổi slug; không tự gán lại bài viết cũ.

Không chạy lại schema/seed trên database VPS có dữ liệu. Tác giả chọn danh mục
mới khi viết bài; chỉ bài đã xuất bản trong danh mục đang hoạt động mới xuất hiện.

Database cũ còn các danh mục `cong-nghe`, `giao-duc`, `doi-song` chạy migration
`migrations/2026_09_08_move_legacy_categories.sql` một lần. Script chuyển bài sang
Tin khoa, Học tập, Cơ hội rồi chỉ xóa danh mục cũ khi không còn bài tham chiếu;
ID bài, bình luận và các mục trong Impact Box được giữ nguyên.

Ảnh upload vẫn là file trên máy chạy PHP. Dùng chung MySQL không đồng bộ file ảnh;
cần chép ảnh tương ứng hoặc thiết lập kho ảnh chung ở đợt triển khai riêng.
