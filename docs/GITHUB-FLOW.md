# Quy trình GitHub của nhóm

## Branch chính

- `main`: bản ổn định, đã chạy thử và có thể trình bày.
- `develop`: nơi ghép các chức năng trước khi đưa vào main.
- `feature/...`: một chức năng hoặc một phạm vi của thành viên.

Branch hiện tại `feature/person1-auth-home` hoàn thiện phạm vi Người 1. Không
đẩy trực tiếp branch này vào `main` khi chưa kiểm tra cùng các branch khác.

## Luồng làm việc

```text
main → develop → feature/... → Pull Request vào develop
→ kiểm thử chung → Pull Request từ develop vào main
```

Mỗi người trước khi làm phải cập nhật `develop`, tạo branch mới, commit bằng
đúng tên/email của mình, push và tạo Pull Request. Nhóm trưởng review file thay
đổi, xử lý xung đột trên branch tích hợp và chạy checklist trước khi merge.

## Quy tắc commit

- Một commit giải quyết một ý rõ ràng.
- Nội dung ví dụ: `feat(auth): them dang ky tai khoan reader`.
- Không commit mật khẩu database, file `.env`, log hoặc cấu hình IDE.
- Không dùng chung Git identity vì giảng viên cần thấy đóng góp từng người.
- Không xóa branch cũ trước khi nội dung đã có trong `main` và nhóm kiểm tra.

## Cách tổ chức lại repository hiện tại

1. Hoàn thiện và kiểm thử `feature/person1-auth-home`.
2. Tạo `develop` từ nền đã thống nhất.
3. Merge Người 1 vào `develop`.
4. Lần lượt đưa bình luận và giao diện/tìm kiếm vào `develop`, sửa xung đột.
5. Kiểm thử toàn bộ role, CSDL và trang công khai.
6. Tạo Pull Request `develop` → `main`.

Các branch `btap-buoi-2`, `btap-buoi-3`, `binhnguyen-dangnhap` là lịch sử cũ;
không merge lặp nếu nội dung đã nằm trong branch mới hơn.
