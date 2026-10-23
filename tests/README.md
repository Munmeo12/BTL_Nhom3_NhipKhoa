# Kiểm thử ghép tìm kiếm

Yêu cầu: PHP có PDO MySQL, MySQL/MariaDB local tại 127.0.0.1:3306 với tài khoản
root không mật khẩu dành cho máy phát triển, Node.js, Playwright và Chrome.
Các script không đọc mật khẩu VPS và không sửa database dùng chung.

1. Tạo database thử mới, tên bắt đầu bằng `nhip_khoa_test_`:

```powershell
C:\xampp\php\php.exe tests/public-search-fixture.php nhip_khoa_test_timkiem_01
```

Script từ chối ghi đè database đã tồn tại. Nó tạo dữ liệu thử gồm 4 vai trò,
bài công khai, bài nháp/chờ duyệt/từ chối và danh mục ẩn; chạy migration danh mục
hai lần để xác minh không tạo trùng.

2. Chạy kiểm thử trình duyệt:

```powershell
node tests/public-search-browser.cjs nhip_khoa_test_timkiem_01
```

Nếu Playwright chưa có trong đường dẫn module Node, đặt biến `PLAYWRIGHT_MODULE`
trỏ tới thư mục package đã cài. Có thể đặt `PHP_BINARY` và `CHROME_BINARY`
để dùng runtime khác đường dẫn XAMPP/Chrome mặc định trong script.

Script mở hai PHP server local tại cổng 8017 và 8018, rồi tự đóng server/trình
duyệt khi kết thúc. Cổng 8018 dùng kết nối database lỗi để kiểm tra HTTP 503.
Ảnh kiểm thử nằm trong thư mục tạm `nhip-khoa-timkiem-qa`.

Kiểm tra: tìm kiếm tiếng Việt/ký tự đặc biệt, phân trang và giữ ngữ cảnh,
lọc trạng thái bài và danh mục, nội dung không thực thi HTML/script, đường dẫn cũ,
ảnh dự phòng, menu mobile, không tràn ngang ở 375/768/1440px, phiên đăng nhập
4 vai trò và đăng xuất. Phần chuyển pending → published chỉ thay đổi dữ liệu thử,
sau đó trả bài về pending. Database thử được giữ lại để tái hiện kết quả.

## Kiểm thử tích hợp trên database VPS chung

Chỉ chạy khi nhóm đã cho phép tạo dữ liệu thử tạm thời trên VPS:

```powershell
node tests/ly-shared-browser.cjs --allow-shared-vps
```

Script chỉ tạo bản ghi có tiền tố `[TEST] qa-ly-...`, kiểm tra quản lý danh mục,
bình luận, Impact Box, phân quyền, đường dẫn gốc/thư mục con và giao diện responsive.
Nó tự xóa đúng các bản ghi thử khi kết thúc, đồng thời so sánh dấu vân tay để bảo
đảm dữ liệu có sẵn của nhóm không bị thay đổi. Không chạy lại schema hoặc seed.
