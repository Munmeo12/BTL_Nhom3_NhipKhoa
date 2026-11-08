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
<?php $roleNames = ['reader' => 'Độc giả', 'author' => 'Tác giả', 'editor' => 'Biên tập viên']; foreach ($users as $user): $protected = $user['role'] === 'admin'; ?><tr>
<td><div class="user-cell"><span><?= avatarInner($user['full_name'], $user['avatar'] ?? null) ?></span><div><b><?= e($user['full_name']) ?></b><small><?= e($user['email']) ?></small></div></div></td>
<td><?php if ($protected): ?><span class="role-chip admin-role">Quản trị viên</span><?php else: ?><span class="role-chip"><?= e($roleNames[$user['role']] ?? $user['role']) ?></span><?php endif; ?></td>
<td><span class="status-chip <?= e($user['status']) ?>"><?= $user['status'] === 'active' ? 'Hoạt động' : 'Đã khóa' ?></span></td><td><?= e(date('d/m/Y', strtotime($user['created_at']))) ?></td>
<td><?php if (!$protected): ?><div class="account-actions"><?php if ($user['status'] === 'active'): ?><button class="status-action role-open" type="button" data-id="<?= (int) $user['id'] ?>" data-name="<?= e($user['full_name']) ?>" data-role="<?= e($user['role']) ?>">Đổi vai trò</button><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>"><input type="hidden" name="action" value="toggle_status"><button class="status-action <?= $user['status'] === 'active' ? 'lock' : 'unlock' ?>" type="submit"><?= $user['status'] === 'active' ? 'Khóa' : 'Mở khóa' ?></button></form></div><?php else: ?><span class="protected-note">Được bảo vệ</span><?php endif; ?></td></tr><?php endforeach; ?>
<?php if ($users === []): ?><tr><td colspan="5"><div class="admin-empty"><strong>Không tìm thấy tài khoản</strong><span>Hãy thử từ khóa hoặc bộ lọc khác.</span></div></td></tr><?php endif; ?>
</tbody></table></div></section>


<div class="admin-modal" id="roleModal" role="dialog" aria-modal="true" aria-labelledby="roleModalTitle"><div class="admin-modal-box">
<header><h2 id="roleModalTitle">Đổi vai trò</h2><button type="button" data-close aria-label="Đóng">✕</button></header>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="change_role"><input type="hidden" name="user_id" id="roleUserId">
<p id="roleModalText" style="margin:0 0 14px;font-size:13px;line-height:1.6"></p>
<label for="roleSelect">Vai trò mới</label>
<select name="role" id="roleSelect" class="role-select"><option value="reader">Độc giả</option><option value="author">Tác giả</option><option value="editor">Biên tập viên</option></select>
<p style="margin:12px 0 0;color:#747f92;font-size:12px;line-height:1.5">Vai trò mới có hiệu lực ngay ở lần tải trang tiếp theo của người đó. Quyền Quản trị viên không cấp ở đây.</p>
<div class="admin-modal-actions"><button type="button" class="status-action view" data-close>Hủy</button><button type="submit" class="status-action" style="color:#fff;background:#1b3a6e">Xác nhận đổi</button></div></form></div></div>
<script>
(function(){var m=document.getElementById('roleModal');if(!m)return;var names={reader:'Độc giả',author:'Tác giả',editor:'Biên tập viên'};
document.querySelectorAll('.role-open').forEach(function(b){b.addEventListener('click',function(){
document.getElementById('roleUserId').value=b.dataset.id;var sel=document.getElementById('roleSelect');sel.value=b.dataset.role;
var t=document.getElementById('roleModalText');t.textContent='';t.append('Đổi vai trò của ');var st=document.createElement('strong');st.textContent=b.dataset.name;t.append(st,' (hiện là '+(names[b.dataset.role]||b.dataset.role)+').');
m.classList.add('show');sel.focus();});});
m.querySelectorAll('[data-close]').forEach(function(b){b.addEventListener('click',function(){m.classList.remove('show');});});
m.addEventListener('click',function(e){if(e.target===m)m.classList.remove('show');});
document.addEventListener('keydown',function(e){if(e.key==='Escape')m.classList.remove('show');});})();
</script>
