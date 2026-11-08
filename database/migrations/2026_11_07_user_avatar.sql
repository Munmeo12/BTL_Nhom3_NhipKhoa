-- Ảnh đại diện của người dùng (tên file trong assets/uploads/avatars/).
-- Chạy an toàn nhiều lần: chỉ thêm cột khi chưa có.
SET @has_avatar := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'avatar'
);
SET @sql := IF(@has_avatar = 0,
    'ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER full_name',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
