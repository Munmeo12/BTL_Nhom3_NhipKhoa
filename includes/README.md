# Includes

Các phần PHP dùng chung.

- `header.php` / `footer.php`: khung trang công khai (khách và độc giả), được `layouts/public.php` nạp.
- `panel-header.php` / `panel-footer.php`: khung có menu trái cho **Admin, Biên tập viên, Tác giả**.
  Menu thay đổi theo vai trò. `author-header.php`, `editor-header.php` và `admin/_header.php`
  chỉ nạp lại khung này.
- `role-footer.php`: phần cuối chung cho trang theo vai trò.
- `public-posts.php`: hàm hỗ trợ trang công khai (đường dẫn ảnh, link quay lại danh sách).
- `app.php`, `auth.php`: lớp tương thích cho code cũ, chuyển sang `bootstrap/app.php` và `AuthService`.
