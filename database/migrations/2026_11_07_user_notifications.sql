-- Thông báo gửi tới từng người dùng (đổi vai trò, duyệt/từ chối yêu cầu làm tác giả).
-- Mỗi thông báo chỉ hiện một lần ở lần tải trang tiếp theo của người nhận.
CREATE TABLE IF NOT EXISTS user_notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    message VARCHAR(600) NOT NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_user_notifications_unread (user_id, read_at),
    CONSTRAINT fk_user_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;
