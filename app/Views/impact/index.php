<?php
declare(strict_types=1);
?>

<style>

/* =========================================================
   IMPACT BOX
========================================================= */

.impact-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 35px 20px 50px;
}

.impact-heading {
    margin-bottom: 20px;
}

.impact-heading h1 {
    margin: 0 0 7px;
    font-size: 25px;
    color: #222;
}

.impact-heading p {
    margin: 0;
    color: #666;
    font-size: 13px;
}


/* =========================================================
   TOOLBAR
========================================================= */

.impact-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.impact-search {
    flex: 1;
    min-width: 230px;
    height: 34px;
    border: 1px solid #ccc;
    border-radius: 3px;
    padding: 0 10px;
    font-size: 12px;
    background: white;
}

.search-button,
.explore-button {
    height: 34px;
    padding: 0 15px;
    border: none;
    border-radius: 3px;
    background: #7d2e24;
    color: white;
    font-size: 12px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

.search-button:hover,
.explore-button:hover {
    background: #65241c;
}

.impact-select {
    height: 34px;
    min-width: 120px;
    border: 1px solid #ccc;
    background: white;
    padding: 0 8px;
    font-size: 12px;
    border-radius: 3px;
}


/* =========================================================
   LIST
========================================================= */

.impact-list {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
}


/* =========================================================
   CARD
========================================================= */

.impact-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 400px;
}

.card-image-link {
    display: block;
    height: 150px;
    flex-shrink: 0;
    text-decoration: none;
    color: inherit;
}

.impact-card-image,
.impact-card-no-image {
    width: 100%;
    height: 150px;
    display: block;
    object-fit: cover;
}

.impact-card-no-image {
    background: #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 12px;
}


/* =========================================================
   CARD BODY
========================================================= */

.impact-card-body {
    padding: 0;
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
}

.impact-card-content {
    height: 150px;
    flex-shrink: 0;
    padding: 10px;
    overflow: hidden;
}

.impact-card-actions {
    height: 150px;
    flex-shrink: 0;
    padding: 10px;
    border-top: 1px solid #eee;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    overflow: hidden;
}


/* =========================================================
   CATEGORY
========================================================= */

.impact-category {
    display: inline-block;
    width: fit-content;
    margin-bottom: 7px;
    padding: 3px 7px;
    background: #f2e5e1;
    color: #7d2e24;
    font-size: 9px;
    font-weight: bold;
    border-radius: 2px;
}


/* =========================================================
   TITLE
========================================================= */

.impact-card-title {
    margin: 0;
    height: 41px;
    font-size: 15px;
    line-height: 1.35;

    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;

    overflow: hidden;
}

.impact-card-title a {
    color: #222;
    text-decoration: none;
}

.impact-card-title a:hover {
    color: #7d2e24;
}


/* =========================================================
   NOTE
========================================================= */

.impact-note {
    background: #fafafa;
    border-left: 3px solid #7d2e24;
    padding: 6px 7px;
    margin: 4px 0 0;
    font-size: 11px;
    line-height: 1.35;
    color: #555;

    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;

    overflow: hidden;
}

.no-note {
    color: #888;
    font-size: 11px;
    margin: 5px 0 8px;
}


/* =========================================================
   META
========================================================= */

.impact-meta {
    height: 25px;
    margin: 4px 0 0;
    padding: 4px 0 0;
    border-top: 1px solid #eee;

    display: flex;
    justify-content: space-between;
    align-items: center;

    font-size: 9px;
    color: #888;

    flex-shrink: 0;
}

.impact-meta .impact-date {
    margin: 0;
    font-size: 9px;
    color: #888;
}

.impact-saved {
    color: #7d2e24;
    font-size: 10px;
    font-weight: bold;
}


/* =========================================================
   ACTION BUTTONS
========================================================= */

.impact-actions {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.impact-actions a,
.impact-actions button,
.note-button {
    border: none;
    border-radius: 3px;
    padding: 6px 8px;
    font-size: 10px;
    cursor: pointer;
    text-decoration: none;
    line-height: 1.2;
}

.read-button {
    background: white;
    color: #7d2e24;
    border: 1px solid #7d2e24 !important;
}

.read-button:hover {
    background: #f6eeeb;
}

.edit-button {
    background: #7d2e24;
    color: white;
}

.delete-button {
    background: #f1eeee;
    color: #7d2e24;
    border: 1px solid #ddd !important;
}

.delete-button:hover {
    background: #e4d8d4;
}


/* =========================================================
   EMPTY
========================================================= */

.impact-empty {
    background: #fffaf7;
    border: 1px solid #e4ddd8;
    min-height: 220px;

    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    text-align: center;
    padding: 30px;
}

.empty-icon {
    width: 52px;
    height: 52px;

    border: 2px dashed #e0a16f;
    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    position: relative;

    color: #e0a16f;

    margin-bottom: 13px;

    font-size: 24px;
}

.empty-plus {
    position: absolute;
    right: -5px;
    bottom: -3px;

    width: 16px;
    height: 16px;

    background: #e0a16f;
    color: white;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 14px;
    font-weight: bold;
    line-height: 1;
}

.impact-empty h2 {
    margin: 0 0 7px;
    font-size: 16px;
}

.impact-empty p {
    margin: 0 0 15px;
    color: #777;
    font-size: 11px;
}


/* =========================================================
   PAGINATION
========================================================= */

.impact-pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.impact-pagination span,
.impact-pagination a {
    min-width: 25px;
    height: 25px;

    display: flex;
    justify-content: center;
    align-items: center;

    border: 1px solid #ddd;
    background: white;
    color: #555;

    text-decoration: none;
    font-size: 11px;
    border-radius: 2px;
}

.impact-pagination .active {
    background: #7d2e24;
    color: white;
    border-color: #7d2e24;
}

.impact-pagination .disabled {
    color: #bbb;
    background: #f7f7f7;
}


/* =========================================================
   MODAL
========================================================= */

.note-modal {
    display: none;

    position: fixed;
    inset: 0;

    background: rgba(0, 0, 0, 0.48);

    align-items: center;
    justify-content: center;

    z-index: 9999;
}

.note-modal-box {
    width: 430px;
    max-width: calc(100% - 30px);

    background: #fffaf5;

    padding: 24px;

    position: relative;

    border-radius: 3px;

    box-shadow:
        0 10px 35px rgba(0, 0, 0, 0.25);
}

.note-modal-close {
    position: absolute;

    top: 12px;
    right: 15px;

    border: none;
    background: none;

    font-size: 20px;

    cursor: pointer;

    color: #222;
}

.note-modal-box h2 {
    margin: 0 0 7px;
    font-size: 17px;
}

.note-modal-description {
    color: #666;

    font-size: 11px;

    padding-bottom: 12px;

    border-bottom: 1px solid #ddd;

    margin-bottom: 13px;
}

.note-form-group {
    margin-bottom: 15px;
}

.note-form-group label {
    display: block;

    margin-bottom: 6px;

    font-weight: bold;

    font-size: 11px;
}

.note-form-group textarea {
    width: 100%;

    min-height: 75px;

    padding: 9px;

    border: 1px solid #ccc;

    resize: vertical;

    font-family: Arial, sans-serif;

    font-size: 11px;

    background: white;
}

.note-counter {
    text-align: right;

    color: #888;

    font-size: 9px;

    margin-top: 3px;
}

.note-modal-actions {
    display: flex;

    justify-content: flex-end;

    gap: 8px;
}

.modal-cancel,
.modal-submit {
    padding: 8px 15px;

    border-radius: 2px;

    cursor: pointer;

    font-size: 10px;
}

.modal-cancel {
    background: white;
    border: 1px solid #ccc;
}

.modal-submit {
    background: #7d2e24;
    border: 1px solid #7d2e24;
    color: white;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .impact-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {

    .impact-list {
        grid-template-columns: 1fr;
    }

    .impact-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .impact-search,
    .search-button,
    .impact-select,
    .explore-button {
        width: 100%;
    }

    .note-modal-box {
        width: calc(100% - 30px);
        padding: 20px;
    }
}

</style>


<!-- =========================================================
     MAIN
========================================================= -->

<div class="impact-container">

    <section class="impact-heading">

        <h1>
            Impact Box
        </h1>

        <p>
            Những bài viết bạn đã lưu để xem lại sau.
        </p>

    </section>


    <!-- =====================================================
         TOOLBAR
    ====================================================== -->

    <?php if (!empty($items)): ?>

        <div class="impact-toolbar">

            <input
                type="text"
                id="impactSearch"
                class="impact-search"
                placeholder="Tìm kiếm trong Impact Box..."
                autocomplete="off"
            >


            <button
                type="button"
                id="impactSearchButton"
                class="search-button"
            >
                🔍 Tìm kiếm
            </button>


            <select
                id="impactCategory"
                class="impact-select"
            >

                <option value="all">
                    Tất cả danh mục
                </option>

                <?php

                $categories = [];

                foreach ($items as $item) {

                    $catName =
                        $item['category_name']
                        ?? '';

                    if (
                        $catName !== ''
                        &&
                        !in_array(
                            $catName,
                            $categories,
                            true
                        )
                    ) {

                        $categories[] = $catName;
                    }
                }

                foreach ($categories as $cat):

                ?>

                    <option
                        value="<?= htmlspecialchars(
                            strtolower($cat),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                        <?= htmlspecialchars(
                            $cat,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>


            <select
                id="impactSort"
                class="impact-select"
            >

                <option value="newest">
                    Mới nhất
                </option>

                <option value="oldest">
                    Cũ nhất
                </option>

            </select>


            <a
                href="<?= e($basePath ?? '') ?>index.php"
                class="explore-button"
            >
                Khám phá thêm bài viết
            </a>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         EMPTY
    ====================================================== -->

    <?php if (empty($items)): ?>

        <section class="impact-empty">

            <div class="empty-icon">

                🔖

                <span class="empty-plus">
                    +
                </span>

            </div>


            <h2>

                Chưa có bài viết nào

                <br>

                trong Impact Box

            </h2>


            <p>

                Bạn chưa lưu bài viết nào.

                Hãy khám phá các bài viết mới và lưu lại
                những nội dung bạn muốn xem sau.

            </p>


            <a
                href="<?= e(BASE_URL) ?>index.php"
                class="explore-button"
            >
                Khám phá bài viết
            </a>

        </section>


    <?php else: ?>


        <!-- =================================================
             LIST
        ================================================== -->

        <section
            class="impact-list"
            id="impactList"
        >

            <?php foreach ($pageItems as $item): ?>

                <?php

                $title =
                    $item['title']
                    ?? '';

                $slug =
                    $item['slug']
                    ?? '';

                $note =
                    $item['note']
                    ?? '';

                $thumbnail =
                    $item['thumbnail']
                    ?? '';

                $createdAt =
                    $item['created_at']
                    ?? '';

                $category =
                    $item['category_name']
                    ?? 'Impact Box';

                $postId =
                    (int) (
                        $item['post_id']
                        ?? 0
                    );

                ?>


                <article
                    class="impact-card"

                    data-title="<?= htmlspecialchars(
                        strtolower($title),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"

                    data-category="<?= htmlspecialchars(
                        strtolower($category),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"

                    data-date="<?= htmlspecialchars(
                        $createdAt,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >


                    <!-- =====================================
                         IMAGE
                    ====================================== -->

                    <a
    href="<?= BASE_URL ?>bai-viet.php?id=<?= $postId ?>"
    class="card-image-link"
>
                        <?php if (!empty($thumbnail)): ?>

                            <img
                                class="impact-card-image"

                                src="<?= e(publicPostImage((string) $thumbnail, BASE_URL)) ?>"

                                alt="<?= htmlspecialchars(
                                    $title,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                        <?php else: ?>

                            <div class="impact-card-no-image">
                                Không có ảnh
                            </div>

                        <?php endif; ?>

                    </a>


                    <!-- =====================================
                         BODY
                    ====================================== -->

                    <div class="impact-card-body">


                        <div class="impact-card-content">


                            <!-- CATEGORY -->

                            <span class="impact-category">

                                <?= htmlspecialchars(
                                    $category,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>


                            <!-- TITLE -->

                            <h2 class="impact-card-title">

                                <a
                                    href="<?= BASE_URL ?>bai-viet.php?id=<?= $postId ?>"
                                >

                                    <?= htmlspecialchars(
                                        $title,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </a>

                            </h2>


                            <!-- META -->

                            <div class="impact-meta">

                                <span class="impact-saved">
                                    🔖 Đã lưu
                                </span>


                                <span class="impact-date">

                                    <?php

                                    if (!empty($createdAt)) {

                                        $timestamp =
                                            strtotime(
                                                $createdAt
                                            );

                                        if (
                                            $timestamp !== false
                                        ) {

                                            echo date(
                                                'd/m/Y • H:i',
                                                $timestamp
                                            );
                                        }
                                    }

                                    ?>

                                </span>

                            </div>


                            <!-- NOTE -->

                            <?php if (!empty($note)): ?>

                                <div class="impact-note">

                                    <strong>
                                        Ghi chú:
                                    </strong>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $note,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                    ) ?>

                                </div>

                            <?php else: ?>

                                <p class="no-note">
                                    Chưa có ghi chú.
                                </p>

                            <?php endif; ?>

                        </div>


                        <!-- =================================
                             ACTIONS
                        ================================== -->

                        <div class="impact-card-actions">


                            <!-- NOTE -->

                            <?php if (!empty($note)): ?>

                                <div class="impact-actions">

                                    <button
                                        type="button"
                                        class="edit-button"

                                        onclick='openEditNote(
                                            <?= $postId ?>,
                                            <?= json_encode(
                                                $note,
                                                JSON_HEX_TAG |
                                                JSON_HEX_AMP |
                                                JSON_HEX_APOS |
                                                JSON_HEX_QUOT
                                            ) ?>
                                        )'
                                    >
                                        Sửa ghi chú
                                    </button>


                                    <!-- CLEAR NOTE -->

                                    <form
                                        method="POST"
                                        action="<?= e(BASE_URL) ?>impact-box-action.php"
                                        style="display:inline;"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e(csrfToken()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="clear_note"
                                        >

                                        <input
                                            type="hidden"
                                            name="post_id"
                                            value="<?= $postId ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="delete-button"

                                            onclick="return confirm(
                                                'Bạn có chắc muốn xóa ghi chú này?'
                                            )"
                                        >
                                            Xóa ghi chú
                                        </button>

                                    </form>

                                </div>


                            <?php else: ?>


                                <!-- ADD NOTE -->

                                <div class="impact-actions">

                                    <button
                                        type="button"
                                        class="edit-button note-button"

                                        onclick="openAddNote(
                                            <?= $postId ?>
                                        )"
                                    >
                                        Thêm ghi chú
                                    </button>

                                </div>

                            <?php endif; ?>


                            <!-- =================================
                                 DELETE IMPACT BOX
                            ================================== -->

                            <div class="impact-actions">

                                <form
                                    method="POST"
                                    action="<?= e(BASE_URL) ?>impact-box-action.php"
                                    style="display:inline;"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrfToken()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="post_id"
                                        value="<?= $postId ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="delete-button"

                                        onclick="return confirm(
                                            'Bạn có chắc muốn xóa bài viết này khỏi Impact Box?'
                                        )"
                                    >
                                        Xóa khỏi Impact Box
                                    </button>

                                </form>

                            </div>


                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </section>


        <!-- =================================================
             PAGINATION
        ================================================== -->

        <?php if ($totalPages > 1): ?>

            <div class="impact-pagination">


                <!-- PREVIOUS -->

                <?php if ($currentPage > 1): ?>

                    <a
                        href="?page=<?= $currentPage - 1 ?>"
                    >
                        ‹
                    </a>

                <?php else: ?>

                    <span class="disabled">
                        ‹
                    </span>

                <?php endif; ?>


                <!-- PAGES -->

                <?php for (
                    $page = 1;
                    $page <= $totalPages;
                    $page++
                ): ?>

                    <?php if ($page === $currentPage): ?>

                        <span class="active">
                            <?= $page ?>
                        </span>

                    <?php else: ?>

                        <a
                            href="?page=<?= $page ?>"
                        >
                            <?= $page ?>
                        </a>

                    <?php endif; ?>

                <?php endfor; ?>


                <!-- NEXT -->

                <?php if ($currentPage < $totalPages): ?>

                    <a
                        href="?page=<?= $currentPage + 1 ?>"
                    >
                        ›
                    </a>

                <?php else: ?>

                    <span class="disabled">
                        ›
                    </span>

                <?php endif; ?>


            </div>

        <?php endif; ?>


    <?php endif; ?>


</div>


<!-- =========================================================
     MODAL
========================================================= -->

<div
    id="noteModal"
    class="note-modal"
>

    <div class="note-modal-box">


        <button
            type="button"
            class="note-modal-close"

            onclick="closeNoteModal()"
        >
            ×
        </button>


        <h2 id="noteModalTitle">
            Lưu vào Impact Box
        </h2>


        <div class="note-modal-description">
            Bạn có thể thêm ghi chú cho bài viết này.
        </div>


        <form
            method="POST"
            action="<?= e(BASE_URL) ?>impact-box-action.php"
        >

            <!-- CSRF -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrfToken()) ?>"
            >

            <input
                type="hidden"
                name="action"
                value="update_note"
            >

            <input
                type="hidden"
                name="post_id"
                id="notePostId"
                value=""
            >


            <div class="note-form-group">

                <label for="noteInput">
                    Ghi chú (Không bắt buộc)
                </label>

                <textarea
                    id="noteInput"
                    name="note"
                    maxlength="200"
                    placeholder="Nhập ghi chú của bạn..."
                ></textarea>

                <div
                    class="note-counter"
                    id="noteCounter"
                >
                    0/200
                </div>

            </div>


            <div class="note-modal-actions">

                <button
                    type="button"
                    class="modal-cancel"

                    onclick="closeNoteModal()"
                >
                    Hủy
                </button>


                <button
                    type="submit"
                    class="modal-submit"
                >
                    Lưu thay đổi
                </button>

            </div>

        </form>

    </div>

</div>


<script>

/* =========================================================
   MODAL THÊM GHI CHÚ
========================================================= */

function openAddNote(postId) {

    const title =
        document.getElementById(
            'noteModalTitle'
        );

    const postIdInput =
        document.getElementById(
            'notePostId'
        );

    const noteInput =
        document.getElementById(
            'noteInput'
        );

    const modal =
        document.getElementById(
            'noteModal'
        );

    if (
        !title ||
        !postIdInput ||
        !noteInput ||
        !modal
    ) {
        return;
    }

    title.textContent =
        'Lưu vào Impact Box';

    postIdInput.value =
        postId;

    noteInput.value =
        '';

    updateNoteCounter();

    modal.style.display =
        'flex';
}


/* =========================================================
   MODAL SỬA GHI CHÚ
========================================================= */

function openEditNote(postId, note) {

    const title =
        document.getElementById(
            'noteModalTitle'
        );

    const postIdInput =
        document.getElementById(
            'notePostId'
        );

    const noteInput =
        document.getElementById(
            'noteInput'
        );

    const modal =
        document.getElementById(
            'noteModal'
        );

    if (
        !title ||
        !postIdInput ||
        !noteInput ||
        !modal
    ) {
        return;
    }

    title.textContent =
        'Sửa ghi chú';

    postIdInput.value =
        postId;

    noteInput.value =
        note || '';

    updateNoteCounter();

    modal.style.display =
        'flex';
}


/* =========================================================
   ĐÓNG MODAL
========================================================= */

function closeNoteModal() {

    const modal =
        document.getElementById(
            'noteModal'
        );

    if (!modal) {
        return;
    }

    modal.style.display =
        'none';
}


/* =========================================================
   COUNTER
========================================================= */

function updateNoteCounter() {

    const textarea =
        document.getElementById(
            'noteInput'
        );

    const counter =
        document.getElementById(
            'noteCounter'
        );

    if (
        !textarea ||
        !counter
    ) {
        return;
    }

    counter.textContent =
        textarea.value.length +
        '/200';
}


const noteInput =
    document.getElementById(
        'noteInput'
    );

if (noteInput) {

    noteInput.addEventListener(
        'input',
        updateNoteCounter
    );
}


/* =========================================================
   CLICK NGOÀI MODAL
========================================================= */

window.addEventListener(
    'click',
    function(event) {

        const modal =
            document.getElementById(
                'noteModal'
            );

        if (
            modal &&
            event.target === modal
        ) {

            closeNoteModal();
        }
    }
);


/* =========================================================
   ESC
========================================================= */

document.addEventListener(
    'keydown',
    function(event) {

        if (event.key === 'Escape') {

            closeNoteModal();
        }
    }
);


/* =========================================================
   SEARCH
========================================================= */

const searchInput =
    document.getElementById(
        'impactSearch'
    );

const searchButton =
    document.getElementById(
        'impactSearchButton'
    );

const categorySelect =
    document.getElementById(
        'impactCategory'
    );

const sortSelect =
    document.getElementById(
        'impactSort'
    );

const impactList =
    document.getElementById(
        'impactList'
    );


/* =========================================================
   FILTER
========================================================= */

function filterImpactCards() {

    if (!impactList) {
        return;
    }

    const keyword =
        searchInput
            ? searchInput.value
                .trim()
                .toLowerCase()
            : '';

    const selectedCategory =
        categorySelect
            ? categorySelect.value
            : 'all';

    const cards =
        Array.from(
            impactList.querySelectorAll(
                '.impact-card'
            )
        );

    cards.forEach(
        function(card) {

            const title =
                card.dataset.title || '';

            const category =
                card.dataset.category || '';

            const matchKeyword =
                keyword === '' ||
                title.includes(keyword);

            const matchCategory =
                selectedCategory === 'all' ||
                category === selectedCategory;

            if (
                matchKeyword &&
                matchCategory
            ) {

                card.style.display =
                    '';

            } else {

                card.style.display =
                    'none';
            }
        }
    );
}


/* =========================================================
   SEARCH BUTTON
========================================================= */

if (searchButton) {

    searchButton.addEventListener(
        'click',
        filterImpactCards
    );
}


/* =========================================================
   ENTER
========================================================= */

if (searchInput) {

    searchInput.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Enter') {

                event.preventDefault();

                filterImpactCards();
            }
        }
    );
}


/* =========================================================
   CATEGORY
========================================================= */

if (categorySelect) {

    categorySelect.addEventListener(
        'change',
        filterImpactCards
    );
}


/* =========================================================
   SORT
========================================================= */

if (
    sortSelect &&
    impactList
) {

    sortSelect.addEventListener(
        'change',
        function() {

            const cards =
                Array.from(
                    impactList.querySelectorAll(
                        '.impact-card'
                    )
                );

            cards.sort(
                function(a, b) {

                    const dateA =
                        new Date(
                            a.dataset.date
                        );

                    const dateB =
                        new Date(
                            b.dataset.date
                        );

                    if (
                        this.value ===
                        'oldest'
                    ) {

                        return dateA - dateB;
                    }

                    return dateB - dateA;

                }.bind(this)
            );

            cards.forEach(
                function(card) {

                    impactList.appendChild(
                        card
                    );
                }
            );
        }
    );
}

</script>



