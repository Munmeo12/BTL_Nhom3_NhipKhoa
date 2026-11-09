<?php declare(strict_types=1); ?>
<section class="admin-heading"><div><p>NỘI DUNG TRANG CHỦ</p><h1>Thông báo chạy</h1><span>Các thông báo đang bật sẽ chạy vòng ở đầu mọi trang công khai (thông báo mới nhất đứng trước).</span></div></section>
<?php if ($success !== ''): ?><div class="admin-alert success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="admin-alert error"><?= e($error) ?></div><?php endif; ?>

<div class="tag-admin-grid">
<section class="admin-panel tag-form-panel">
<div class="tag-form-head"><h2><?= $editing ? 'Sửa thông báo' : 'Thêm thông báo mới' ?></h2></div>
<form method="post" class="tag-form" novalidate>
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<input type="hidden" name="announcement_id" value="<?= (int) ($editing['id'] ?? 0) ?>">
<label for="ann-content">Nội dung <span>*</span></label>
<textarea id="ann-content" name="content" rows="4" maxlength="255" required placeholder="Ví dụ: Kỳ đăng ký học phần bắt đầu từ ngày 20/05."><?= e($formContent) ?></textarea>
<small>Tối đa 255 ký tự. Hệ thống tự thêm chữ "Thông báo:" phía trước.</small>
<div class="tag-form-actions">
<button class="filter-button" type="submit" name="action" value="save"><?= $editing ? 'Lưu thay đổi' : 'Thêm thông báo' ?></button>
<?php if ($editing): ?><a class="status-action view" href="<?= e(BASE_URL) ?>admin/thong-bao.php">Hủy</a><?php endif; ?>
</div>
</form>
</section>

<section class="admin-panel">
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Nội dung</th><th>Trạng thái</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach ($items as $item): ?><tr>
<td><?= e($item['content']) ?></td>
<td><?= (int) $item['is_active'] === 1 ? '<b>Đang hiển thị</b>' : 'Đã tắt' ?></td>
<td><?= e(date('d/m/Y H:i', strtotime((string) $item['created_at']))) ?></td>
<td><div class="tag-row-actions">
<a class="status-action view" href="<?= e(BASE_URL) ?>admin/thong-bao.php?edit=<?= (int) $item['id'] ?>">Sửa</a>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="announcement_id" value="<?= (int) $item['id'] ?>"><button class="status-action view" type="submit" name="action" value="toggle"><?= (int) $item['is_active'] === 1 ? 'Tắt' : 'Bật' ?></button></form>
<form method="post" onsubmit="return confirm('Xóa thông báo này?')"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="announcement_id" value="<?= (int) $item['id'] ?>"><button class="status-action lock" type="submit" name="action" value="delete">Xóa</button></form>
</div></td>
</tr><?php endforeach; ?>
<?php if (!$items): ?><tr><td colspan="4"><div class="admin-empty"><strong>Chưa có thông báo nào</strong><span>Thanh thông báo sẽ tự ẩn khi không có thông báo đang bật.</span></div></td></tr><?php endif; ?>
</tbody></table></div>
</section>
</div>
