<?php declare(strict_types=1); ?>
<section class="admin-heading"><div><p>PHÂN LOẠI NỘI DUNG</p><h1>Quản lý thẻ</h1><span>Thẻ gắn chủ đề cho bài viết để độc giả lọc bài theo chủ đề.</span></div></section>
<?php if ($success !== ''): ?><div class="admin-alert success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="admin-alert error"><?= e($error) ?></div><?php endif; ?>

<div class="tag-admin-grid">
<section class="admin-panel tag-form-panel">
<div class="tag-form-head"><h2><?= $editing ? 'Sửa thẻ' : 'Thêm thẻ mới' ?></h2></div>
<form method="post" class="tag-form" novalidate>
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<input type="hidden" name="tag_id" value="<?= (int) ($editing['id'] ?? 0) ?>">
<label for="tag-name">Tên thẻ <span>*</span></label>
<input id="tag-name" type="text" name="name" maxlength="50" value="<?= e($formName) ?>" placeholder="Ví dụ: Lập trình web" required>
<small>Slug tự tạo từ tên thẻ<?= $editing ? ' (hiện tại: ' . e($editing['slug']) . ')' : '' ?>.</small>
<div class="tag-form-actions">
<button class="filter-button" type="submit" name="action" value="save"><?= $editing ? 'Lưu thay đổi' : 'Thêm thẻ' ?></button>
<?php if ($editing): ?><a class="status-action view" href="<?= e(BASE_URL) ?>admin/the.php">Hủy</a><?php endif; ?>
</div>
</form>
</section>

<section class="admin-panel">
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Tên thẻ</th><th>Slug</th><th>Số bài</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach ($tags as $tag): ?><tr>
<td><b><?= e($tag['name']) ?></b></td>
<td><?= e($tag['slug']) ?></td>
<td><?= (int) $tag['post_count'] ?></td>
<td><div class="tag-row-actions">
<a class="status-action view" href="<?= e(BASE_URL) ?>admin/the.php?edit=<?= (int) $tag['id'] ?>">Sửa</a>
<form method="post" onsubmit="return confirm('Xóa thẻ này? Thẻ sẽ bị gỡ khỏi <?= (int) $tag['post_count'] ?> bài viết.')"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="tag_id" value="<?= (int) $tag['id'] ?>"><button class="status-action lock" type="submit" name="action" value="delete">Xóa</button></form>
</div></td>
</tr><?php endforeach; ?>
<?php if (!$tags): ?><tr><td colspan="4"><div class="admin-empty"><strong>Chưa có thẻ nào</strong><span>Hãy thêm thẻ đầu tiên ở khung bên trái.</span></div></td></tr><?php endif; ?>
</tbody></table></div>
</section>
</div>
