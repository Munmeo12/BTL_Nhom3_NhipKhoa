<?php
declare(strict_types=1);
?>

<style>
    /* =========================
       SAVE BUTTON
    ========================= */

    .article-save-area {
        margin: 20px 0 30px;
    }

    .article-save-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        padding: 10px 18px;

        border: 1px solid #ddd;
        border-radius: 8px;

        background: #fff;
        color: #333;

        font-size: 15px;
        font-weight: 600;

        cursor: pointer;

        transition:
            background 0.2s,
            color 0.2s,
            border-color 0.2s;
    }

    .article-save-button:hover {
        background: #f5f5f5;
        border-color: #bbb;
    }

    /* =========================
       MODAL
    ========================= */

    .save-modal {
        display: none;
        position: fixed;
        inset: 0;

        z-index: 9999;

        align-items: center;
        justify-content: center;

        padding: 20px;

        background: rgba(0, 0, 0, 0.45);
    }

    .save-modal.show {
        display: flex;
    }

    .save-modal-box {
        position: relative;

        width: 100%;
        max-width: 480px;

        padding: 28px;

        border-radius: 14px;

        background: #fff;

        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
    }

    .save-modal-close {
        position: absolute;

        top: 12px;
        right: 15px;

        border: none;
        background: transparent;

        font-size: 28px;
        line-height: 1;

        color: #777;

        cursor: pointer;
    }

    .save-modal-box h2 {
        margin: 0 0 8px;
    }

    .save-modal-description {
        margin-bottom: 20px;

        color: #666;
        font-size: 14px;
    }

    .save-note-label {
        display: block;

        margin-bottom: 8px;

        font-weight: 600;
    }

    .save-note {
        width: 100%;
        min-height: 120px;

        padding: 12px;

        border: 1px solid #ddd;
        border-radius: 8px;

        resize: vertical;

        font-family: inherit;
        font-size: 14px;

        box-sizing: border-box;
    }

    .save-note:focus {
        outline: none;
        border-color: #888;
    }

    .save-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;

        margin-top: 20px;
    }

    .save-cancel,
    .save-submit {
        padding: 10px 18px;

        border-radius: 8px;

        cursor: pointer;

        font-size: 14px;
        font-weight: 600;
    }

    .save-cancel {
        border: 1px solid #ddd;
        background: #fff;
        color: #555;
    }

    .save-submit {
        border: none;
        background: #222;
        color: #fff;
    }

    .save-submit:hover {
        opacity: 0.9;
    }
</style>

<section class="public-pages">

    <div class="site-shell">

        <a
            class="public-back"
            href="<?= e(publicBackUrl($context)) ?>"
        >
            ← Quay lại danh sách bài viết
        </a>

        <?php if ($loadError): ?>

            <div
                class="public-empty"
                role="alert"
            >
                <h1>Chưa thể tải bài viết</h1>

                <p>
                    Kết nối dữ liệu đang gián đoạn.
                    Vui lòng thử lại sau.
                </p>

                <a
                    href="<?= e(
                        publicDetailUrl(
                            (int) $postId,
                            $context
                        )
                    ) ?>"
                >
                    Thử lại
                </a>
            </div>

        <?php elseif (!$post): ?>

            <div class="public-empty">

                <h1>
                    Không tìm thấy bài viết
                </h1>

                <p>
                    Bài viết không tồn tại,
                    chưa được duyệt hoặc đã bị ẩn.
                </p>

            </div>

        <?php else: ?>

            <div class="public-layout">

                <article class="public-detail-body">

                    <p class="public-eyebrow">
                        <?= e($post['category_name']) ?>
                    </p>

                    <h1>
                        <?= e($post['title']) ?>
                    </h1>

                    <p class="public-byline">
                        <?= e(publicPostDate($post)) ?>
                        · Tác giả:
                        <?= e($post['author_name']) ?>
                    </p>

                    <img
                        class="public-cover"
                        src="<?= e(
                            publicPostImage(
                                $post['thumbnail']
                            )
                        ) ?>"
                        alt=""
                        data-public-image
                    >

                    <!-- =========================
                         NÚT LƯU BÀI
                    ========================== -->

                    <div class="article-save-area">

                        <?php if (!empty($_SESSION['user'])): ?>

                            <button
                                type="button"
                                class="article-save-button"
                                onclick="openSaveModal()"
                            >
                                ♡
                                <span>Lưu vào Impact Box</span>
                            </button>

                        <?php else: ?>

                            <a
                                href="dang-nhap.php"
                                class="article-save-button"
                            >
                                ♡
                                <span>Đăng nhập để lưu</span>
                            </a>

                        <?php endif; ?>

                    </div>

                    <p class="public-summary">
                        <?= e($post['summary']) ?>
                    </p>

                    <div class="public-content">
                        <?= nl2br(e($post['content'])) ?>
                    </div>

                    <section class="article-comments" aria-labelledby="comments-title">
                        <h2 id="comments-title">Bình luận (<?= count($comments) ?>)</h2>

                        <?php if ($commentSuccess !== ''): ?>
                            <div class="comment-alert comment-alert-success" role="status">
                                <?= e($commentSuccess) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($commentError !== ''): ?>
                            <div class="comment-alert comment-alert-error" role="alert">
                                <?= e($commentError) ?>
                            </div>
                        <?php endif; ?>

                        <div class="comments-list">
                            <?php if (!$comments): ?>
                                <p class="comments-empty">Chưa có bình luận đã được duyệt.</p>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <article class="comment-item">
                                        <div class="comment-avatar" aria-hidden="true">
                                            <?= e(mb_substr((string) ($comment['full_name'] ?? 'N'), 0, 1)) ?>
                                        </div>
                                        <div class="comment-body">
                                            <strong><?= e($comment['full_name'] ?? 'Người dùng') ?></strong>
                                            <p><?= nl2br(e($comment['content'])) ?></p>
                                            <small><?= e(date('d/m/Y H:i', strtotime((string) $comment['created_at']))) ?></small>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($currentUser === null): ?>
                            <div class="comment-login-prompt">
                                <p>Vui lòng đăng nhập để tham gia bình luận.</p>
                                <a href="<?= e(BASE_URL) ?>dang-nhap.php">Đăng nhập</a>
                            </div>
                        <?php else: ?>
                            <form method="post" class="comment-form" id="commentForm" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                <p class="comment-user">
                                    Bình luận với tên <strong><?= e($currentUser['full_name'] ?? 'Người dùng') ?></strong>
                                </p>
                                <label for="commentContent">Nội dung bình luận</label>
                                <textarea
                                    name="content"
                                    id="commentContent"
                                    maxlength="1000"
                                    placeholder="Viết bình luận..."
                                    required
                                ><?= e($commentContent) ?></textarea>
                                <span class="comment-client-error" id="commentContentError" aria-live="polite"></span>
                                <button type="submit" name="comment_submit">Gửi bình luận</button>
                            </form>
                        <?php endif; ?>
                    </section>

                </article>

                <aside class="public-sidebar">

                    <section>

                        <h2>
                            Bài viết liên quan
                        </h2>

                        <?php foreach ($related as $item): ?>

                            <a
                                class="public-related"
                                href="<?= e(
                                    publicDetailUrl(
                                        (int) $item['id'],
                                        $context
                                    )
                                ) ?>"
                            >

                                <img
                                    src="<?= e(
                                        publicPostImage(
                                            $item['thumbnail']
                                        )
                                    ) ?>"
                                    alt=""
                                    data-public-image
                                >

                                <span>
                                    <?= e($item['title']) ?>

                                    <small>
                                        <?= e(
                                            publicPostDate(
                                                $item
                                            )
                                        ) ?>
                                    </small>
                                </span>

                            </a>

                        <?php endforeach; ?>

                        <?php if (!$related): ?>

                            <p>
                                Chưa có bài viết liên quan.
                            </p>

                        <?php endif; ?>

                    </section>

                    <section>

                        <h2>
                            Danh mục
                        </h2>

                        <nav
                            aria-label="Danh mục bài viết"
                        >

                            <?php foreach (
                                PUBLIC_CATEGORIES
                                as $slug => $name
                            ): ?>

                                <a
                                    href="pages/<?= e($slug) ?>.php"
                                >
                                    <?= e($name) ?>
                                </a>

                            <?php endforeach; ?>

                        </nav>

                    </section>

                </aside>

            </div>

        <?php endif; ?>

    </div>

</section>


<?php if ($post && !empty($_SESSION['user'])): ?>

    <!-- =====================================================
         MODAL LƯU VÀO IMPACT BOX
    ====================================================== -->

    <div
        id="saveModal"
        class="save-modal"
        onclick="closeSaveModalByOverlay(event)"
    >

        <div
            class="save-modal-box"
            onclick="event.stopPropagation()"
        >

            <button
                type="button"
                class="save-modal-close"
                onclick="closeSaveModal()"
                aria-label="Đóng"
            >
                ×
            </button>

            <h2>
                Lưu vào Impact Box
            </h2>
            <p class="save-modal-description">
                Bạn có thể thêm ghi chú cho bài viết này.
            </p>

            <form
                method="POST"
                action="impact-box-action.php"
            >

                <!-- CSRF -->

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrfToken()) ?>"
                >

                <!-- ACTION -->

                <input
                    type="hidden"
                    name="action"
                    value="add"
                >

                <!-- POST ID -->

                <input
                    type="hidden"
                    name="post_id"
                    value="<?= (int) $postId ?>"
                >

                <label
                    class="save-note-label"
                    for="saveNote"
                >
                    Ghi chú
                    <span style="font-weight: normal;">
                        (không bắt buộc)
                    </span>
                </label>

                <textarea
                    id="saveNote"
                    name="note"
                    class="save-note"
                    maxlength="200"
                    placeholder="Nhập ghi chú cho bài viết..."
                ></textarea>

                <div class="save-modal-actions">

                    <button
                        type="button"
                        class="save-cancel"
                        onclick="closeSaveModal()"
                    >
                        Hủy
                    </button>

                    <button
                        type="submit"
                        class="save-submit"
                    >
                        Lưu bài viết
                    </button>

                </div>

            </form>

        </div>

    </div>

<?php endif; ?>


<script>

    function openSaveModal() {

        const modal =
            document.getElementById('saveModal');

        if (!modal) {
            return;
        }

        modal.classList.add('show');

        const note =
            document.getElementById('saveNote');

        if (note) {
            note.focus();
        }
    }


    function closeSaveModal() {

        const modal =
            document.getElementById('saveModal');

        if (!modal) {
            return;
        }

        modal.classList.remove('show');
    }


    function closeSaveModalByOverlay(event) {

        if (event.target.id === 'saveModal') {
            closeSaveModal();
        }
    }


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {
                closeSaveModal();
            }

        }
    );

</script>

<script>
    document.getElementById('commentForm')?.addEventListener('submit', function (event) {
        const field = document.getElementById('commentContent');
        const error = document.getElementById('commentContentError');

        if (!field || !error) {
            return;
        }

        if (field.value.trim() === '') {
            event.preventDefault();
            error.textContent = 'Vui lòng nhập nội dung bình luận.';
            field.focus();
        } else {
            error.textContent = '';
        }
    });
</script>


