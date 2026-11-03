# Includes

Thư mục chứa các phần PHP dùng chung như header và footer.



## Khung giao diện có menu trái

- `panel-header.php` / `panel-footer.php`: khung dùng chung cho **Admin, Biên tập viên, Tác giả** (menu dọc bên trái, mục **Trang chủ** quay về trang công khai). Menu thay đổi theo vai trò.
- `author-header.php`, `editor-header.php`, `admin/_header.php` chỉ nạp lại khung này. Độc giả vẫn dùng `header.php` công khai (nút "Làm tác giả" nằm trên header).
