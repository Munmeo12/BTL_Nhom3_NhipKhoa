<section class="admin-heading"><div><p>XIN CHÀO, <?= e($adminName) ?></p><h1>Tổng quan hệ thống</h1><span>Theo dõi nhanh tài khoản và nội dung của Nhịp Khoa.</span></div><a class="admin-primary" href="users.php">Quản lý tài khoản →</a></section>
<?php if ($databaseError !== ''): ?><div class="admin-alert error"><?= e($databaseError) ?></div><?php endif; ?>
<section class="admin-stats" aria-label="Thống kê hệ thống">
<article><span class="stat-icon wine">♙</span><div><small>Tổng tài khoản</small><strong><?= $counts['users'] ?></strong><p>Người dùng trong hệ thống</p></div></article>
<article><span class="stat-icon gold">▤</span><div><small>Tổng bài viết</small><strong><?= $counts['posts'] ?></strong><p>Tất cả trạng thái bài</p></div></article>
<article><span class="stat-icon green">✓</span><div><small>Chờ duyệt</small><strong><?= $counts['pending'] ?></strong><p>Cần biên tập viên xử lý</p></div></article>
<article><span class="stat-icon gray">⊘</span><div><small>Tài khoản khóa</small><strong><?= $counts['locked'] ?></strong><p>Không thể đăng nhập</p></div></article>
</section>
<section class="admin-panel"><header><div><h2>Tài khoản mới</h2><p>Năm tài khoản được tạo gần đây nhất</p></div><a href="users.php">Xem tất cả →</a></header><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Người dùng</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th></tr></thead><tbody>
<?php foreach ($recentUsers as $user): ?><tr><td><div class="user-cell"><span><?= e(mb_strtoupper(mb_substr($user['full_name'], 0, 1))) ?></span><div><b><?= e($user['full_name']) ?></b><small><?= e($user['email']) ?></small></div></div></td><td><span class="role-chip"><?= e($user['role_name']) ?></span></td><td><span class="status-chip <?= e($user['status']) ?>"><?= $user['status'] === 'active' ? 'Hoạt động' : 'Đã khóa' ?></span></td><td><?= e(date('d/m/Y', strtotime($user['created_at']))) ?></td></tr><?php endforeach; ?>
</tbody></table></div></section>

