USE nhip_khoa;

INSERT INTO users (id, role_id, email, password_hash, full_name, status, created_at) VALUES
    (7, 3, 'tacgia.b@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Lê Quốc Bảo', 'active', '2026-08-20 09:00:00'),
    (8, 4, 'docgia.demo@nhipkhoa.vn', '$2y$10$5qOBuv285ychc3kts46L7OsmFyodolU33/K0T8QAo3dczDCzkqFga', 'Phan Thu Hà', 'active', '2026-08-26 09:00:00');

INSERT INTO tags (id, slug, name) VALUES
    (1, 'php', 'PHP'),
    (2, 'mysql', 'MySQL'),
    (3, 'hoc-bong', 'Học bổng'),
    (4, 'thuc-tap', 'Thực tập'),
    (5, 'workshop', 'Workshop'),
    (6, 'github', 'GitHub');

INSERT INTO posts (id, category_id, author_id, reviewer_id, title, slug, summary, content, status, editor_note, published_at, created_at) VALUES
    (3, 2, 3, NULL, 'Cách ôn thi môn Cơ sở dữ liệu hiệu quả', 'cach-on-thi-co-so-du-lieu', 'Lộ trình ôn tập từ chuẩn hóa đến JOIN.', 'Bài đang chờ biên tập viên duyệt.', 'pending', NULL, NULL, '2026-08-24 08:00:00'),
    (4, 1, 3, 2, 'Bài viết chưa đạt yêu cầu', 'bai-viet-chua-dat-yeu-cau', 'Bài bị từ chối để kiểm thử luồng chỉnh sửa.', 'Nội dung quá ngắn.', 'rejected', 'Cần bổ sung nguồn tham khảo và ví dụ minh họa.', NULL, '2026-08-24 09:00:00'),
    (5, 3, 7, 2, 'Học bổng toàn phần cho sinh viên CNTT', 'hoc-bong-toan-phan-cntt', 'Thông tin học bổng và hạn nộp hồ sơ.', 'Chi tiết điều kiện, hồ sơ và hạn nộp học bổng.', 'published', NULL, '2026-08-25 09:00:00', '2026-08-25 07:00:00'),
    (6, 3, 7, NULL, 'Bản nháp tuyển thực tập sinh', 'ban-nhap-tuyen-thuc-tap-sinh', 'Bản nháp của Tác giả B, chưa gửi duyệt.', 'Nội dung đang soạn.', 'draft', NULL, NULL, '2026-08-25 10:00:00'),
    (7, 4, 7, 2, 'Workshop Git và GitHub cho người mới', 'workshop-git-github-nguoi-moi', 'Hướng dẫn làm việc nhóm với Git.', 'Buổi workshop thực hành tạo repo, branch và pull request.', 'published', NULL, '2026-08-26 14:00:00', '2026-08-26 08:00:00'),
    (8, 1, 3, 2, 'Khoa công bố lịch thi học kỳ', 'khoa-cong-bo-lich-thi-hoc-ky', 'Lịch thi chính thức học kỳ này.', 'Sinh viên xem lịch thi tại cổng thông tin.', 'published', NULL, '2026-08-27 08:00:00', '2026-08-27 07:00:00'),
    (9, 1, 3, 2, 'Thông báo đăng ký học phần', 'thong-bao-dang-ky-hoc-phan', 'Thời gian và hướng dẫn đăng ký học phần.', 'Đăng ký từ ngày 20/05 trên hệ thống.', 'published', NULL, '2026-08-28 08:00:00', '2026-08-28 07:00:00'),
    (10, 1, 3, 2, 'Kết quả cuộc thi lập trình sinh viên', 'ket-qua-cuoc-thi-lap-trinh', 'Danh sách đội đạt giải.', 'Chúc mừng các đội đã đạt giải.', 'published', NULL, '2026-08-29 08:00:00', '2026-08-29 07:00:00'),
    (11, 1, 7, 2, 'Phòng máy mở cửa thêm buổi tối', 'phong-may-mo-cua-buoi-toi', 'Lịch mở phòng máy mới.', 'Phòng máy mở đến 21h các ngày trong tuần.', 'published', NULL, '2026-08-30 08:00:00', '2026-08-30 07:00:00'),
    (12, 1, 7, 2, 'Ra mắt câu lạc bộ lập trình', 'ra-mat-cau-lac-bo-lap-trinh', 'CLB lập trình tuyển thành viên.', 'Đăng ký tham gia trước cuối tháng.', 'published', NULL, '2026-08-31 08:00:00', '2026-08-31 07:00:00'),
    (13, 1, 3, 2, 'Thay đổi quy định đồ án tốt nghiệp', 'thay-doi-quy-dinh-do-an-tot-nghiep', 'Điểm mới trong quy định đồ án.', 'Sinh viên đọc kỹ quy định mới trước khi đăng ký.', 'published', NULL, '2026-09-01 08:00:00', '2026-09-01 07:00:00');

INSERT INTO post_tags (post_id, tag_id) VALUES
    (1, 1), (1, 2), (1, 6),
    (2, 2),
    (3, 2),
    (5, 3), (5, 4),
    (7, 5), (7, 6);

INSERT INTO comments (post_id, user_id, content, status, created_at) VALUES
    (1, 4, 'Cảm ơn tác giả, bài rất hữu ích.', 'approved', '2026-08-22 11:00:00'),
    (1, 4, '<script>alert("XSS")</script>', 'pending', '2026-08-22 11:30:00'),
    (5, 4, 'Cho mình hỏi hạn nộp hồ sơ là ngày nào?', 'pending', '2026-08-25 12:00:00'),
    (7, 4, 'Bình luận vi phạm, quảng cáo spam.', 'hidden', '2026-08-26 16:00:00'),
    (7, 2, 'Workshop rất thực tế.', 'approved', '2026-08-26 17:00:00');

INSERT INTO impact_box_items (user_id, post_id, note, created_at) VALUES
    (4, 5, 'Chuẩn bị hồ sơ trước hạn nộp.', '2026-08-25 13:00:00'),
    (4, 7, NULL, '2026-08-26 18:00:00');

INSERT INTO author_requests (user_id, contact, topics, reason, sample_link, status, admin_note, reviewed_by, reviewed_at, created_at) VALUES
    (4, NULL, 'Học tập', 'Em muốn chia sẻ kinh nghiệm học môn lập trình web cho các bạn khóa sau.', NULL, 'rejected', 'Hồ sơ còn sơ sài, hãy bổ sung bài viết mẫu rồi gửi lại.', 1, '2026-08-27 09:00:00', '2026-08-26 20:00:00'),
    (8, 'Zalo 0900000000', 'Học bổng, thực tập, workshop', 'Mình là sinh viên năm 3, thường xuyên tham gia workshop và muốn đăng bài tổng hợp học bổng, cơ hội thực tập cho các bạn trong khoa.', 'https://example.com/bai-mau', 'pending', NULL, NULL, NULL, '2026-08-28 08:30:00');
-- Dữ liệu mẫu cho lượt xem và bài nổi bật (chức năng nâng cao).
UPDATE posts SET view_count = 128, is_featured = 1 WHERE id = 1;
UPDATE posts SET view_count = 86 WHERE id = 5;
UPDATE posts SET view_count = 54 WHERE id = 7;
UPDATE posts SET view_count = 31 WHERE id = 13;
