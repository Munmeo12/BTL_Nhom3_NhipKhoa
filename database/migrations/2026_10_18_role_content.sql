USE nhip_khoa;

-- Chạy file này trên CSDL đã tạo trước khi tích hợp giao diện Tác giả/Biên tập viên.
ALTER TABLE posts
    ADD COLUMN IF NOT EXISTS thumbnail VARCHAR(255) NULL AFTER summary,
    ADD COLUMN IF NOT EXISTS editor_note TEXT NULL AFTER status;
