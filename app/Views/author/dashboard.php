<?php
$totalPosts = $counts['total'];
$draftPosts = $counts['draft'];
$pendingPosts = $counts['pending'];
$publishedPosts = $counts['published'];
$rejectedPosts = $counts['rejected'];
?>

<div class="dashboard-container">

    <!-- Lời chào -->
    <section class="welcome-section">
        <h1>
            Xin chào,
            <?= htmlspecialchars($_SESSION['full_name'] ?? 'Tác giả') ?>!
        </h1>
        <p>Quản lý và theo dõi các bài viết của bạn.</p>
    </section>

    <!-- Thống kê -->
    <section class="statistics">

        <div class="stat-card">
            <div class="stat-icon stat-blue">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <div class="stat-info">
                <strong><?= $totalPosts ?></strong>
                <span>Tổng số bài</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-yellow">
                <i class="fa-regular fa-clipboard"></i>
            </div>
            <div class="stat-info">
                <strong><?= $draftPosts ?></strong>
                <span>Nháp</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-purple">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="stat-info">
                <strong><?= $pendingPosts ?></strong>
                <span>Chờ duyệt</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-green">
                <i class="fa-regular fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <strong><?= $publishedPosts ?></strong>
                <span>Đã đăng</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-red">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <div class="stat-info">
                <strong><?= $rejectedPosts ?></strong>
                <span>Từ chối</span>
            </div>
        </div>

    </section>

    <!-- Bài viết gần đây -->
    <section class="recent-posts">

        <div class="recent-header">
            <h2>Bài viết gần đây</h2>

            <a href="<?= BASE_URL ?>author/posts.php"
               class="view-all">
                Xem tất cả
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="table-wrapper">

            <table class="posts-table">

                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Chuyên mục</th>
                        <th>Ngày cập nhật</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($recentPosts)): ?>

                    <?php foreach ($recentPosts as $post): ?>

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

                            <td>
                                <?= htmlspecialchars($post['title']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $post['category_name']
                                    ?? 'Chưa phân loại'
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

                            <td>

                                <?php if (
                                    $post['status'] === 'draft'
                                    || $post['status'] === 'rejected'
                                ): ?>

                                    <a
                                        href="<?= BASE_URL ?>author/edit.php?id=<?= $post['id'] ?>"
                                        class="action-button">
                                        Sửa
                                    </a>

                                <?php else: ?>

                                    <a
                                        href="<?= BASE_URL ?>author/view.php?id=<?= $post['id'] ?>"
                                        class="action-button">
                                        Xem
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="empty-post">
                            Bạn chưa có bài viết nào.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>

