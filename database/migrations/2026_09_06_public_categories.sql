-- Chạy trên database Nhịp Khoa đã chọn. Không thay thế dữ liệu hiện có.
INSERT INTO categories (slug, name, status)
SELECT 'tin-khoa', 'Tin khoa', 'active'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug='tin-khoa' OR name='Tin khoa');
INSERT INTO categories (slug, name, status)
SELECT 'hoc-tap', 'Học tập', 'active'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug='hoc-tap' OR name='Học tập');
INSERT INTO categories (slug, name, status)
SELECT 'co-hoi', 'Cơ hội', 'active'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug='co-hoi' OR name='Cơ hội');
INSERT INTO categories (slug, name, status)
SELECT 'su-kien', 'Sự kiện', 'active'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug='su-kien' OR name='Sự kiện');
