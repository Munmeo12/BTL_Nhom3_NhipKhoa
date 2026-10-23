# Người 1 — nền tảng hệ thống, tài khoản và trang chủ

## 1. Phạm vi nhiệm vụ

Người 1 (Trần Nguyễn Bình Nguyên) chịu trách nhiệm phần nền mà các thành viên
khác cùng sử dụng:

1. Kết nối MySQL bằng PDO.
2. Đăng ký tài khoản độc giả.
3. Đăng nhập, đăng xuất và session.
4. Xác thực và phân quyền theo role.
5. Header/footer và trang chủ công khai.
6. Khung dashboard Admin.
7. Schema/seed chung, tích hợp Git và kiểm thử toàn hệ thống.

Người 1 không làm thay nghiệp vụ viết/duyệt bài, tìm kiếm, bình luận, danh mục
hay Impact Box. Người 1 chỉ cung cấp hàm dùng chung để các chức năng đó biết ai
đang đăng nhập và người ấy được phép làm gì.

## 2. Flow đăng ký

```text
Khách mở dang-ky.php
→ nhập họ tên, email, mật khẩu, nhập lại mật khẩu
→ PHP kiểm tra CSRF và dữ liệu
→ password_hash() mã hóa mật khẩu
→ câu lệnh PDO prepared statement ghi users
→ server tự lấy role reader
→ chuyển sang trang đăng nhập
```

Không có trường chọn role. Dù người dùng sửa HTML, câu SQL vẫn chỉ lấy role có
`code = 'reader'`. Admin/editor/author phải do Admin cấp trong khu vực quản trị.

Điều kiện đăng ký:

- Họ tên 2–120 ký tự.
- Email hợp lệ, tối đa 150 ký tự và không được trùng.
- Mật khẩu 8–72 ký tự, có ít nhất một chữ và một số.
- Nhập lại mật khẩu phải khớp.
- Mật khẩu chỉ lưu dưới dạng hash, không lưu chữ thường.

## 3. Flow đăng nhập

```text
Người dùng nhập email + mật khẩu
→ kiểm tra CSRF và định dạng
→ SELECT user JOIN roles bằng email
→ password_verify() so sánh mật khẩu với hash
→ kiểm tra status = active
→ đổi session ID để chống session fixation
→ lưu id, email, full_name, role vào session
→ Admin đến dashboard; role khác về trang chủ
```

Thông báo sai dùng chung “Email hoặc mật khẩu không chính xác” để người ngoài
không đoán được email nào có trong hệ thống.

## 4. Flow quyền truy cập

```text
Guest:  xem trang chủ và bài published; không bình luận/lưu bài/quản trị
Reader: quyền Guest + bình luận và Impact Box
Author: quyền Reader + quản lý bài của chính mình
Editor: xem và duyệt/từ chối/xuất bản bài chờ duyệt
Admin: quản lý tài khoản, role, danh mục và toàn bộ hệ thống
```

Ẩn nút trên giao diện chỉ giúp dễ dùng, không phải bảo mật. Mỗi trang nhạy cảm
phải gọi `requireRole([...])` ở đầu file. Ví dụ dashboard gọi
`requireRole(['admin'])`. Người chưa đăng nhập bị chuyển tới đăng nhập; người đã
đăng nhập nhưng sai role nhận HTTP 403.

## 5. Flow trang chủ

Trang chủ không yêu cầu đăng nhập. PHP truy vấn tối đa 6 bài với điều kiện
`posts.status = 'published'` và danh mục còn hoạt động. Vì kiểm tra nằm trong
SQL nên khách không thể đổi URL để đọc bản nháp. Nếu chưa có bài thì hiển thị
trạng thái rỗng; nếu MySQL chưa chạy thì hiển thị hướng dẫn thay vì lộ lỗi kỹ
thuật.

## 6. Các file quan trọng

- `config/database.php`: tạo một kết nối PDO dùng chung.
- `includes/app.php`: session, encode output, CSRF và redirect.
- `includes/auth.php`: lấy user hiện tại, bắt đăng nhập và kiểm tra role.
- `includes/header.php`, `includes/footer.php`: giao diện chung.
- `dang-ky.php`, `dang-nhap.php`, `dang-xuat.php`: xác thực.
- `index.php`: trang chủ công khai.
- `admin/dashboard.php`: ví dụ trang chỉ Admin được mở.

## 7. Cách demo với giảng viên

1. Mở trang chủ khi chưa đăng nhập: vẫn đọc được bài Published.
2. Thử đăng ký: không có lựa chọn role và tài khoản được tạo là Reader.
3. Thử email trùng/mật khẩu yếu/nhập lại sai để chứng minh validation server.
4. Đăng nhập sai rồi đăng nhập đúng bằng tài khoản seed.
5. Đăng nhập Reader rồi nhập URL `/admin/dashboard.php`: nhận lỗi 403.
6. Đăng nhập Admin: vào được dashboard.
7. Khóa tài khoản trong DB rồi đăng nhập lại: hệ thống từ chối.

## 8. Phần tiếp theo còn phải tích hợp

- Link chi tiết bài khi branch trang công khai được ghép.
- Các trang Author/Editor do Người 2 thực hiện.
- Tìm kiếm/chi tiết/responsive nâng cao do Người 3 thực hiện.
- Bình luận/danh mục/Impact Box do Người 4 thực hiện.

## 9. Flow Admin quản lý tài khoản

Admin đăng nhập và mở `admin/users.php` để tìm tài khoản theo tên hoặc email,
lọc theo vai trò/trạng thái, cấp vai trò Reader, Author hoặc Editor và khóa/mở
khóa tài khoản.

Mọi thao tác cập nhật đều gửi bằng POST, kiểm tra CSRF và gọi
`requireRole(['admin'])` ở server. Admin đang đăng nhập không thể tự khóa hoặc
tự hạ quyền; một Admin khác cũng không thể bị sửa tại màn hình này. Hệ thống
không cho xem hoặc sửa mật khẩu của người dùng.

Flow cấp quyền tác giả:

```text
Người dùng đăng ký → mặc định là Reader
→ Admin mở Quản lý tài khoản
→ chọn vai trò Tác giả và bấm Lưu
→ lần đăng nhập tiếp theo, session nhận role Author
→ người dùng được phép vào Dashboard tác giả
```
