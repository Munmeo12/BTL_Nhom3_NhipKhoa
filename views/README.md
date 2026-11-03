# Thư mục `views/` (ở gốc project)

**Đây không phải nơi chứa giao diện.** Thư mục này chỉ có các *file điểm vào* cho chuyên mục
(`categories.php`, `category-add.php`, `category-edit.php`, `category-delete.php`) và Impact Box
(`impact-box.php`). Mỗi file chỉ nạp `bootstrap/app.php` rồi gọi một controller.

Giao diện thật (HTML) nằm trong `app/Views/`. Giữ riêng thư mục này để các đường dẫn cũ
`views/categories.php`, `views/impact-box.php` vẫn dùng được.
