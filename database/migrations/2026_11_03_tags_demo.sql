USE nhip_khoa;
-- Chạy sau khi bảng tags/post_tags đã có (xem 2026_10_23_add_tags.sql). Chỉ thêm dữ liệu mẫu.

-- Thẻ mẫu và gán thẻ cho bài mẫu
INSERT IGNORE INTO tags (id, slug, name) VALUES
    (1, 'lap-trinh-web', 'Lập trình web'),
    (2, 'co-so-du-lieu', 'Cơ sở dữ liệu'),
    (3, 'hoc-tap', 'Học tập');

INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES
    (1, 1),
    (1, 3),
    (2, 2);
