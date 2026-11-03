USE nhip_khoa;

-- Chức năng nâng cao (đề 2.3, mục I): lượt xem, bài nổi bật, lịch xuất bản.
-- Chạy MỘT lần trên CSDL đã có sẵn. CSDL tạo mới từ schema.sql đã có sẵn các cột này.
--
-- view_count   : số lượt xem bài (tăng khi mở trang chi tiết, mỗi phiên đếm 1 lần).
-- is_featured  : 1 = bài nổi bật, được ưu tiên hiển thị ở đầu trang chủ.
-- Lịch xuất bản: dùng luôn cột published_at có sẵn. Bài status='published' nhưng
--                published_at còn ở tương lai thì CHƯA hiển thị cho khách.
ALTER TABLE posts
    ADD COLUMN view_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER published_at,
    ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER view_count;

CREATE INDEX idx_posts_featured ON posts(is_featured, published_at);
