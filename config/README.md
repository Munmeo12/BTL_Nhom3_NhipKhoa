# Config

- `database.php`: hàm `db()` tạo kết nối PDO. Đọc cấu hình từ biến môi trường
  (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_SSL_CA`, `DB_SSL_VERIFY`)
  trước, rồi tới `config.local.php`, cuối cùng mới dùng giá trị mặc định.
- `config.local.example.php`: file mẫu. Sao chép thành `config.local.php` rồi điền thông tin
  MySQL của máy mình. **Không commit và không gửi `config.local.php`** (đã có trong `.gitignore`).
- `certs/mysql-ca.pem`: chứng chỉ CA công khai, chỉ dùng khi kết nối MySQL trên VPS có SSL.
