USE nhip_khoa;

START TRANSACTION;

-- Chuyển bài từ ba danh mục mẫu cũ sang bốn danh mục đang dùng trên menu.
-- Giữ nguyên ID bài nên bình luận và Impact Box không bị mất liên kết.
UPDATE posts AS post
INNER JOIN categories AS legacy
    ON legacy.id = post.category_id
INNER JOIN categories AS target
    ON target.slug = CASE legacy.slug
        WHEN 'cong-nghe' THEN 'tin-khoa'
        WHEN 'giao-duc' THEN 'hoc-tap'
        WHEN 'doi-song' THEN 'co-hoi'
    END
SET post.category_id = target.id
WHERE legacy.slug IN ('cong-nghe', 'giao-duc', 'doi-song');

-- Chỉ xóa danh mục cũ sau khi chắc chắn không còn bài nào tham chiếu.
DELETE legacy
FROM categories AS legacy
LEFT JOIN posts AS post
    ON post.category_id = legacy.id
WHERE legacy.slug IN ('cong-nghe', 'giao-duc', 'doi-song')
  AND post.id IS NULL;

COMMIT;
