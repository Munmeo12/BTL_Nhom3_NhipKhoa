-- Thông báo chạy (marquee) ở đầu trang, do admin quản lý.
CREATE TABLE IF NOT EXISTS announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO announcements (content, is_active)
SELECT 'Kỳ đăng ký học phần bắt đầu từ ngày 20/05.', 1
WHERE NOT EXISTS (SELECT 1 FROM announcements);
