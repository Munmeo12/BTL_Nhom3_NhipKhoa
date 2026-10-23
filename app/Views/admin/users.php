<section class="admin-heading"><div><p>NGƯỜI DÙNG VÀ PHÂN QUYỀN</p><h1>Quản lý tài khoản</h1><span>Tìm kiếm, cấp vai trò và kiểm soát trạng thái đăng nhập.</span></div></section>
<?php if ($notice !== ''): ?><div class="admin-alert success"><?= e($notice) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="admin-alert error"><?= e($error) ?></div><?php endif; ?>
<section class="admin-panel account-panel">
<form class="admin-filters" method="get">
<label class="admin-search"><span>⌕</span><input type="search" name="q" value="<?= e($keyword) ?>" placeholder="Tìm theo tên hoặc email"></label>
<select name="role" aria-label="Lọc vai trò"><option value="">Tất cả vai trò</option><option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Quản trị viên</option><option value="editor" <?= $roleFilter === 'editor' ? 'selected' : '' ?>>Biên tập viên</option><option value="author" <?= $roleFilter === 'author' ? 'selected' : '' ?>>Tác giả</option><option value="reader" <?= $roleFilter === 'reader' ? 'selected' : '' ?>>Độc giả</option></select>
<select name="status" aria-label="Lọc trạng thái"><option value="">Tất cả trạng thái</option><option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Hoạt động</option><option value="locked" <?= $statusFilter === 'locked' ? 'selected' : '' ?>>Đã khóa</option></select>
<button class="filter-button" type="submit">Lọc tài khoản</button><?php if ($keyword !== '' || $roleFilter !== '' || $statusFilter !== ''): ?><a class="clear-filter" href="users.php">Xóa lọc</a><?php endif; ?>
</form>
<div class="account-summary"><strong><?= count($users) ?></strong><span>tài khoản được hiển thị</span><small>Tài khoản đăng ký mới luôn là Độc giả.</small></div>
<div class="admin-table-wrap"><table class="admin-table account-table"><thead><tr><th>Người dùng</th><th>Vai trò hiện tại</th><th>Trạng thái</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach ($users as $user): $protected = $user['role'] === 'admin'; ?><tr>
<td><div class="user-cell"><span><?= e(mb_strtoupper(mb_substr($user['full_name'], 0, 1))) ?></span><div><b><?= e($user['full_name']) ?></b><small><?= e($user['email']) ?></small></div></div></td>
<td><?php if ($protected): ?><span class="role-chip admin-role">Quản trị viên</span><?php else: ?><form class="role-form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>"><input type="hidden" name="action" value="change_role"><select name="role"><option value="reader" <?= $user['role'] === 'reader' ? 'selected' : '' ?>>Độc giả</option><option value="author" <?= $user['role'] === 'author' ? 'selected' : '' ?>>Tác giả</option><option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Biên tập viên</option></select><button type="submit">Lưu</button></form><?php endif; ?></td>
<td><span class="status-chip <?= e($user['status']) ?>"><?= $user['status'] === 'active' ? 'Hoạt động' : 'Đã khóa' ?></span></td><td><?= e(date('d/m/Y', strtotime($user['created_at']))) ?></td>
<td><?php if (!$protected): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>"><input type="hidden" name="action" value="toggle_status"><button class="status-action <?= $user['status'] === 'active' ? 'lock' : 'unlock' ?>" type="submit"><?= $user['status'] === 'active' ? 'Khóa' : 'Mở khóa' ?></button></form><?php else: ?><span class="protected-note">Được bảo vệ</span><?php endif; ?></td></tr><?php endforeach; ?>
<?php if ($users === []): ?><tr><td colspan="5"><div class="admin-empty"><strong>Không tìm thấy tài khoản</strong><span>Hãy thử từ khóa hoặc bộ lọc khác.</span></div></td></tr><?php endif; ?>
</tbody></table></div></section>

