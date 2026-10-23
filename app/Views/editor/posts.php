<?php declare(strict_types=1); ?>

<div class="review-container">
    <div class="review-heading">
        <h1>Duyệt bài viết</h1>
        <p>Quản lý và kiểm duyệt các bài viết.</p>
    </div>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="filter-tabs">
        <a href="?status=all" class="<?= $filter==='all'?'active':'' ?>">
            Tất cả (<?= (int)$counts['total'] ?>)
        </a>
        <a href="?status=pending" class="<?= $filter==='pending'?'active':'' ?>">
            Chờ duyệt (<?= (int)$counts['pending'] ?>)
        </a>
        <a href="?status=published" class="<?= $filter==='published'?'active':'' ?>">
            Đã duyệt (<?= (int)$counts['published'] ?>)
        </a>
        <a href="?status=rejected" class="<?= $filter==='rejected'?'active':'' ?>">
            Từ chối (<?= (int)$counts['rejected'] ?>)
        </a>
    </div>

    <div class="review-layout">
        <section class="post-list">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Mã bài</th>
                            <th>Tiêu đề</th>
                            <th>Tác giả</th>
                            <th>Chuyên mục</th>
                            <th>Ngày gửi</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($posts): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr class="<?= $viewId===$post['id']?'selected':'' ?>">
                                <td><?= 'BV'.str_pad((string) $post['id'],3,'0',STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="post-title">
                                        <?php if ($post['thumbnail']): ?>
                                            <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($post['thumbnail']) ?>" alt="">
                                        <?php else: ?>
                                            <div class="small-image">
                                                <i class="fa-regular fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($post['title']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($post['author_name']) ?></td>
                                <td><?= htmlspecialchars($post['category_name'] ?? 'Chưa phân loại') ?></td>
                                <td><?= date('d/m/Y',strtotime($post['created_at'])) ?></td>
                                <td>
                                    <a class="view-btn" href="?status=<?= $filter ?>&page=<?= $page ?>&view=<?= $post['id'] ?>">
                                        Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty">Không có bài viết.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i=1;$i<=$totalPages;$i++): ?>
                        <a href="?status=<?= $filter ?>&page=<?= $i ?>"
                           class="<?= $page===$i?'active':'' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="post-detail">
            <?php if ($selectedPost): ?>
                <div class="detail-top">
                    <h3>Chi tiết bài viết</h3>
                </div>

                <div class="detail-content">
                    <h2><?= htmlspecialchars($selectedPost['title']) ?></h2>

                    <div class="meta">
                        <p><b>Tác giả:</b> <?= htmlspecialchars($selectedPost['author_name']) ?></p>
                        <p><b>Chuyên mục:</b> <?= htmlspecialchars($selectedPost['category_name'] ?? 'Chưa phân loại') ?></p>
                        <p><b>Ngày gửi:</b> <?= date('d/m/Y',strtotime($selectedPost['created_at'])) ?></p>
                        <p>
                            <b>Trạng thái:</b>
                            <span class="status <?= $selectedPost['status'] ?>">
                                <?php
                                if ($selectedPost['status']==='pending') echo 'Chờ duyệt';
                                elseif ($selectedPost['status']==='published') echo 'Đã duyệt';
                                else echo 'Từ chối';
                                ?>
                            </span>
                        </p>
                    </div>

                    <?php if ($selectedPost['thumbnail']): ?>
                        <img class="detail-image"
                             src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($selectedPost['thumbnail']) ?>"
                             alt="">
                    <?php endif; ?>

                    <?php if ($selectedPost['summary']): ?>
                        <div class="article-summary">
                            <?= nl2br(e((string) $selectedPost['summary'])) ?>
                        </div>
                    <?php endif; ?>

                    <div class="article-content">
                        <?= nl2br(e((string) $selectedPost['content'])) ?>
                    </div>

                    <?php if ($selectedPost['status']==='rejected' && $selectedPost['editor_note']): ?>
                        <div class="reject-note">
                            <b>Lý do từ chối:</b>
                            <p><?= nl2br(htmlspecialchars($selectedPost['editor_note'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($selectedPost['status']==='pending'): ?>
                    <div class="detail-actions">
                        <button type="button" class="reject-btn" onclick="openReject()">
                            <i class="fa-solid fa-xmark"></i> Từ chối
                        </button>

                        <form method="POST" onsubmit="return confirm('Bạn chắc chắn muốn duyệt bài này?')"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="post_id" value="<?= $selectedPost['id'] ?>">
                            <button type="submit" name="action" value="approve" class="approve-btn">
                                <i class="fa-solid fa-check"></i> Duyệt bài
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="choose-post">
                    <i class="fa-regular fa-file-lines"></i>
                    <p>Chọn một bài viết để xem chi tiết.</p>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php if ($selectedPost && $selectedPost['status']==='pending'): ?>
<div class="reject-modal" id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Từ chối bài viết</h3>
            <button type="button" onclick="closeReject()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="post_id" value="<?= $selectedPost['id'] ?>">

            <div class="modal-body">
                <label for="editor_note">Lý do từ chối <span>*</span></label>
                <textarea id="editor_note" name="editor_note" rows="5"
                          placeholder="Nhập lý do từ chối..." required></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeReject()">Hủy</button>
                <button type="submit" name="action" value="reject" class="confirm-reject">
                    Xác nhận từ chối
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReject(){
    document.getElementById('rejectModal').classList.add('show');
}
function closeReject(){
    document.getElementById('rejectModal').classList.remove('show');
}
window.addEventListener('click',function(e){
    const modal=document.getElementById('rejectModal');
    if(e.target===modal) closeReject();
});
</script>
<?php endif; ?>
