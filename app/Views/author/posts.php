<?php
$totalPosts = $counts['total'];
$draftPosts = $counts['draft'];
$pendingPosts = $counts['pending'];
$publishedPosts = $counts['published'];
$rejectedPosts = $counts['rejected'];
?>

<div class="posts-page-container">

    <div class="posts-page-header">
        <div>
            <h1>Bài viết của tôi</h1>
            <p>Quản lý các bài viết bạn đã tạo.</p>
        </div>

        <a href="<?= BASE_URL ?>author/create.php" class="create-post-button">
            <i class="fa-solid fa-plus"></i>
            Tạo bài viết
        </a>
    </div>

    <div class="post-filter-tabs">

        <a href="?status=all"
           class="<?= $filter === 'all' ? 'active' : '' ?>">
            Tất cả
            <span><?= $totalPosts ?></span>
        </a>

        <a href="?status=draft"
           class="<?= $filter === 'draft' ? 'active' : '' ?>">
            Nháp
            <span><?= $draftPosts ?></span>
        </a>

        <a href="?status=pending"
           class="<?= $filter === 'pending' ? 'active' : '' ?>">
            Chờ duyệt
            <span><?= $pendingPosts ?></span>
        </a>

        <a href="?status=published"
           class="<?= $filter === 'published' ? 'active' : '' ?>">
            Đã đăng
            <span><?= $publishedPosts ?></span>
        </a>

        <a href="?status=rejected"
           class="<?= $filter === 'rejected' ? 'active' : '' ?>">
            Từ chối
            <span><?= $rejectedPosts ?></span>
        </a>

    </div>

    <div class="posts-list-box">

        <div class="table-wrapper">

            <table class="posts-table">

                <thead>
                    <tr>
                        <th>Mã bài</th>
                        <th>Tiêu đề</th>
                        <th>Chuyên mục</th>
                        <th>Ngày cập nhật</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($posts)): ?>

                    <?php foreach ($posts as $post): ?>

                        <?php
                        switch ($post['status']) {
                            case 'draft':
                                $statusClass = 'draft';
                                $statusText = 'Nháp';
                                break;

                            case 'pending':
                                $statusClass = 'pending';
                                $statusText = 'Chờ duyệt';
                                break;

                            case 'published':
                                $statusClass = 'published';
                                $statusText = 'Đã đăng';
                                break;

                            case 'rejected':
                                $statusClass = 'rejected';
                                $statusText = 'Từ chối';
                                break;

                            default:
                                $statusClass = '';
                                $statusText = 'Không xác định';
                        }
                        ?>

                        <tr>

                            <td class="post-code">
                                <?= 'BV' . str_pad((string) $post['id'], 3, '0', STR_PAD_LEFT) ?>
                            </td>

                            <td class="post-title-cell">
                                <?= htmlspecialchars($post['title']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $post['category_name'] ?? 'Chưa phân loại'
                                ) ?>
                            </td>

                            <td>
                                <?= date(
                                    'd/m/Y',
                                    strtotime($post['updated_at'])
                                ) ?>
                            </td>

                            <td>
                                <span class="status <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>

                            <td class="post-actions">

                                <a href="<?= BASE_URL ?>author/view.php?id=<?= $post['id'] ?>"
                                   class="small-action-button">
                                    <i class="fa-regular fa-eye"></i>
                                    Xem
                                </a>

                                <?php if (
                                    $post['status'] === 'draft' ||
                                    $post['status'] === 'rejected'
                                ): ?>

                                    <a href="<?= BASE_URL ?>author/edit.php?id=<?= $post['id'] ?>"
                                       class="small-action-button">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                        Sửa
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="empty-post">
                            Không có bài viết nào.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

