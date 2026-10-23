USE nhip_khoa;

INSERT INTO roles (id, code, name) VALUES
    (1, 'admin', 'Quản trị viên'),
    (2, 'editor', 'Biên tập viên'),
    (3, 'author', 'Tác giả'),
    (4, 'reader', 'Độc giả');

INSERT INTO users (
    id,
    role_id,
    email,
    password_hash,
    full_name,
    status,
    created_at
) VALUES
    (1, 1, 'admin@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Trần Nguyễn Bình Nguyên', 'active', '2026-08-20 08:00:00'),
    (2, 2, 'quynh@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Trần Hà Như Quỳnh', 'active', '2026-08-20 08:10:00'),
    (3, 3, 'tuyet@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Đặng Ánh Tuyết', 'active', '2026-08-20 08:20:00'),
    (4, 4, 'ly@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Khổng Thị Lý', 'active', '2026-08-20 08:30:00'),
    (5, 3, 'khoa.locked@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Nguyễn Minh Khoa', 'locked', '2026-08-20 08:40:00'),
    (6, 4, 'lan.locked@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Phạm Hoàng Lan', 'locked', '2026-08-20 08:50:00');

INSERT INTO categories (id, slug, name, status) VALUES
    (1, 'tin-khoa', 'Tin khoa', 'active'),
    (2, 'hoc-tap', 'Học tập', 'active'),
    (3, 'co-hoi', 'Cơ hội', 'active'),
    (4, 'su-kien', 'Sự kiện', 'active');

INSERT INTO posts (
    id,
    category_id,
    author_id,
    reviewer_id,
    title,
    slug,
    summary,
    content,
    status,
    published_at,
    created_at
) VALUES
    (1, 1, 3, 2, 'Sinh viên học lập trình web như thế nào?', 'sinh-vien-hoc-lap-trinh-web', 'Một số kinh nghiệm học PHP và MySQL cho người mới.', 'Bài viết chia sẻ cách thực hành từng bước với PHP, MySQL và GitHub.', 'published', '2026-08-22 09:00:00', '2026-08-21 08:00:00'),
    (2, 2, 3, NULL, 'Thiết kế cơ sở dữ liệu cho website tin tức', 'thiet-ke-csdl-website-tin-tuc', 'Giới thiệu các bảng chính và mối quan hệ trong Nhịp Khoa.', 'Nội dung đang được tác giả hoàn thiện trước khi gửi duyệt.', 'draft', NULL, '2026-08-23 10:00:00');

INSERT INTO comments (post_id, user_id, content, status, created_at) VALUES
    (1, 4, 'Bài viết dễ hiểu và có ví dụ thực tế.', 'approved', '2026-08-22 10:00:00'),
    (1, 2, 'Cần bổ sung thêm ví dụ về JOIN.', 'pending', '2026-08-22 10:15:00');

INSERT INTO impact_box_items (user_id, post_id, note, created_at) VALUES
    (4, 1, 'Đọc lại phần hướng dẫn MySQL trước buổi học.', '2026-08-22 10:30:00');
