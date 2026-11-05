<?php declare(strict_types=1);
$statusLabels = ['pending' => 'Chờ duyệt', 'published' => 'Đã duyệt', 'rejected' => 'Từ chối'];
$tabs = ['all' => ['Tất cả', 'total'], 'pending' => ['Chờ duyệt', 'pending'], 'published' => ['Đã duyệt', 'published'], 'rejected' => ['Từ chối', 'rejected']];
$isAdmin = (bool) ($isAdmin ?? false);
?>
<div class="review-shell<?= $isAdmin ? '' : ' with-pad' ?>">
<section class="admin-heading"><div><p>KIỂM DUYỆT NỘI DUNG</p><h1>Quản lý bài viết</h1><span>Xem, duyệt hoặc từ chối các bài viết do tác giả gửi lên.</span></div></section>
<?php if ($success !== ''): ?><div class="admin-alert success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="admin-alert error"><?= e($error) ?></div><?php endif; ?>

<div class="post-review-grid<?= $selectedPost ? ' has-detail' : '' ?>">
<section class="admin-panel">
<div class="admin-filters post-tabs" role="tablist" aria-label="Lọc theo trạng thái">
<?php foreach ($tabs as $key => [$label, $countKey]): ?><a class="post-tab<?= $filter === $key ? ' active' : '' ?>" href="?status=<?= e($key) ?>"><?= e($label) ?> <b><?= (int) $counts[$countKey] ?></b></a><?php endforeach; ?>
</div>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Mã bài</th><th>Tiêu đề</th><th>Tác giả</th><th>Chuyên mục</th><th>Ngày gửi</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach ($posts as $post): ?><tr<?= $viewId === (int) $post['id'] ? ' class="row-selected"' : '' ?>>
<td><?= 'BV' . str_pad((string) $post['id'], 3, '0', STR_PAD_LEFT) ?></td>
<td><div class="post-cell"><?php if ($post['thumbnail']): ?><img src="<?= e(BASE_URL) ?>assets/uploads/<?= e($post['thumbnail']) ?>" alt=""><?php else: ?><span class="post-thumb-empty">▤</span><?php endif; ?><b><?= e($post['title']) ?></b></div></td>
<td><?= e($post['author_name']) ?></td>
<td><?= e($post['category_name'] ?? 'Chưa phân loại') ?></td>
<td><?= e(date('d/m/Y', strtotime($post['created_at']))) ?></td>
<td><span class="status-chip post-<?= e($post['status']) ?>"><?= !empty($post['is_scheduled']) ? 'Hẹn đăng' : e($statusLabels[$post['status']] ?? $post['status']) ?></span><?= !empty($post['is_featured']) ? ' <span title="Bài nổi bật" style="color:#9c6b16">★</span>' : '' ?></td>
<td><a class="status-action view" href="?status=<?= e($filter) ?>&amp;page=<?= (int) $page ?>&amp;view=<?= (int) $post['id'] ?>">Xem</a></td>
</tr><?php endforeach; ?>
<?php if (!$posts): ?><tr><td colspan="7"><div class="admin-empty"><strong>Không có bài viết</strong><span>Chưa có bài nào trong mục này.</span></div></td></tr><?php endif; ?>
</tbody></table></div>
<?php if ($totalPages > 1): ?><nav class="admin-pagination" aria-label="Phân trang"><?php for ($i = 1; $i <= $totalPages; $i++): ?><a href="?status=<?= e($filter) ?>&amp;page=<?= $i ?>" class="<?= $page === $i ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
</section>

<?php if ($selectedPost): ?>
<aside class="admin-panel post-detail-panel">
<header><div><h2>Chi tiết bài viết</h2><p>BV<?= str_pad((string) $selectedPost['id'], 3, '0', STR_PAD_LEFT) ?></p></div><a href="?status=<?= e($filter) ?>&amp;page=<?= (int) $page ?>">Đóng ✕</a></header>
<div class="post-detail-body">
<h3><?= e($selectedPost['title']) ?></h3>
<dl class="post-meta">
<div><dt>Tác giả</dt><dd><?= e($selectedPost['author_name']) ?></dd></div>
<div><dt>Chuyên mục</dt><dd><?= e($selectedPost['category_name'] ?? 'Chưa phân loại') ?></dd></div>
<div><dt>Ngày gửi</dt><dd><?= e(date('d/m/Y', strtotime($selectedPost['created_at']))) ?></dd></div>
<div><dt>Trạng thái</dt><dd><span class="status-chip post-<?= e($selectedPost['status']) ?>"><?= !empty($selectedPost['is_scheduled']) ? 'Hẹn đăng' : e($statusLabels[$selectedPost['status']] ?? $selectedPost['status']) ?></span><?= !empty($selectedPost['is_featured']) ? ' <span style="color:#9c6b16">★ Nổi bật</span>' : '' ?></dd></div>
</dl>
<?php if ($selectedPost['thumbnail']): ?><img class="post-detail-image" src="<?= e(BASE_URL) ?>assets/uploads/<?= e($selectedPost['thumbnail']) ?>" alt=""><?php endif; ?>
<?php if ($selectedPost['summary']): ?><p class="post-detail-summary"><?= nl2br(e((string) $selectedPost['summary'])) ?></p><?php endif; ?>
<div class="post-detail-content"><?= nl2br(e((string) $selectedPost['content'])) ?></div>
<?php if ($selectedPost['status'] === 'rejected' && $selectedPost['editor_note']): ?><div class="admin-alert error"><b>Lý do từ chối:</b><br><?= nl2br(e((string) $selectedPost['editor_note'])) ?></div><?php endif; ?>
</div>
<div class="post-detail-actions">
<?php if ($isAdmin): ?><form method="post" onsubmit="return confirm('Xóa vĩnh viễn bài viết này cùng bình luận liên quan?')"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="post_id" value="<?= (int) $selectedPost['id'] ?>"><button class="status-action lock" type="submit" name="action" value="delete">Xóa bài</button></form><?php endif; ?>
<?php if ($selectedPost['status'] === 'pending'): ?>
<button type="button" class="status-action lock" onclick="document.getElementById('rejectModal').classList.add('show')">Từ chối</button>
<form method="post" class="schedule-form" onsubmit="return confirm('Bạn chắc chắn muốn duyệt bài này?')"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="post_id" value="<?= (int) $selectedPost['id'] ?>"><label class="schedule-field">Hẹn giờ đăng (không bắt buộc)<input type="datetime-local" name="publish_at"></label><button class="filter-button" type="submit" name="action" value="approve">Duyệt bài</button></form>
<?php elseif ($selectedPost['status'] === 'published'): ?>
<span class="view-count"><?= (int) $selectedPost['view_count'] ?> lượt xem<?= !empty($selectedPost['is_scheduled']) ? ' · Hẹn đăng ' . e(date('H:i d/m/Y', strtotime((string) $selectedPost['published_at']))) : '' ?></span>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="post_id" value="<?= (int) $selectedPost['id'] ?>"><button class="status-action view" type="submit" name="action" value="<?= $selectedPost['is_featured'] ? 'unfeature' : 'feature' ?>">★ <?= $selectedPost['is_featured'] ? 'Bỏ nổi bật' : 'Đặt nổi bật' ?></button></form>
<?php if ($isAdmin): ?><button type="button" class="filter-button" onclick="document.getElementById('rejectModal').classList.add('show')">Gỡ bài</button><?php endif; ?>
<?php endif; ?>
</div>
</aside>
<?php endif; ?>
</div>

<?php if ($selectedPost && ($selectedPost['status'] === 'pending' || ($isAdmin && $selectedPost['status'] === 'published'))):
    $isUnpublish = $selectedPost['status'] === 'published'; ?>
<div class="admin-modal" id="rejectModal"><div class="admin-modal-box">
<header><h2><?= $isUnpublish ? 'Gỡ bài viết' : 'Từ chối bài viết' ?></h2><button type="button" aria-label="Đóng" onclick="document.getElementById('rejectModal').classList.remove('show')">✕</button></header>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="post_id" value="<?= (int) $selectedPost['id'] ?>">
<label for="editor_note"><?= $isUnpublish ? 'Lý do gỡ bài' : 'Lý do từ chối' ?> <span>*</span></label>
<textarea id="editor_note" name="editor_note" rows="5" placeholder="Nhập lý do từ chối..." required></textarea>
<div class="admin-modal-actions"><button type="button" class="status-action view" onclick="document.getElementById('rejectModal').classList.remove('show')">Hủy</button><button class="status-action lock" type="submit" name="action" value="<?= $isUnpublish ? 'unpublish' : 'reject' ?>"><?= $isUnpublish ? 'Xác nhận gỡ bài' : 'Xác nhận từ chối' ?></button></div>
</form></div></div>
<script>document.getElementById('rejectModal').addEventListener('click',function(e){if(e.target===this)this.classList.remove('show');});</script>
<?php endif; ?>
</div>