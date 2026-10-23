<?php declare(strict_types=1); ?>

<div class="edit-page-container">

    <div class="edit-page-header">

        <a
            href="<?= BASE_URL ?>author/view.php?id=<?= $post['id'] ?>"
            class="back-link"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Quay lại
        </a>

        <h1>Chỉnh sửa bài viết</h1>

        <p>
            Mã bài:
            <strong>
                <?= 'BV' . str_pad(
                    (string) $post['id'],
                    3,
                    '0',
                    STR_PAD_LEFT
                ) ?>
            </strong>
        </p>

    </div>

    <?php if ($success !== ''): ?>

        <div class="form-success">

            <i class="fa-solid fa-circle-check"></i>

            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>

    <?php if ($error !== ''): ?>

        <div class="form-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <?php if (
        !empty($oldEditorNote) &&
        $post['status'] === 'rejected'
    ): ?>

        <div class="rejected-note">

            <div class="rejected-note-title">

                <i class="fa-solid fa-circle-exclamation"></i>

                Lý do bài viết bị từ chối

            </div>

            <p>
                <?= nl2br(
                    htmlspecialchars(
                        $oldEditorNote
                    )
                ) ?>
            </p>

        </div>

    <?php endif; ?>

    <form
        method="POST"
        enctype="multipart/form-data"
        class="edit-post-form"
        id="editPostForm"
    ><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">

            <label for="title">
                Tiêu đề
                <span>*</span>
            </label>

            <input
                type="text"
                id="title"
                name="title"
                value="<?= htmlspecialchars(
                    $values['title']
                ) ?>"
                placeholder="Nhập tiêu đề bài viết..."
            >

        </div>

        <div class="form-group">

            <label for="category_id">
                Chuyên mục
                <span>*</span>
            </label>

            <select
                id="category_id"
                name="category_id"
            >

                <option value="">
                    -- Chọn chuyên mục --
                </option>

                <?php
                $selectedCategory =
                    $values['category_id'];
                ?>

                <?php foreach (
                    $categories as $category
                ): ?>

                    <option
                        value="<?= $category['id'] ?>"
                        <?= $selectedCategory
                            == $category['id']
                            ? 'selected'
                            : '' ?>
                    >

                        <?= htmlspecialchars(
                            $category['name']
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="form-group">

            <label for="summary">
                Tóm tắt
            </label>

            <textarea
                id="summary"
                name="summary"
                rows="3"
                maxlength="300"
                placeholder="Nhập tóm tắt bài viết..."
            ><?= htmlspecialchars(
                $values['summary']
            ) ?></textarea>

            <small>
                Tối đa 300 ký tự.
            </small>

        </div>

        <div class="form-group">

            <label>
                Ảnh đại diện
            </label>

            <?php if (
                !empty($post['thumbnail'])
            ): ?>

                <div class="current-image">

                    <p>Ảnh hiện tại</p>

                    <img
                        src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars(
                            $post['thumbnail']
                        ) ?>"
                        alt="Ảnh đại diện hiện tại"
                    >

                </div>

            <?php endif; ?>

            <label
                class="upload-box"
                for="thumbnail"
            >

                <i class="fa-regular fa-image"></i>

                <div>

                    <strong>
                        Chọn ảnh mới
                    </strong>

                    <p>
                        JPG, PNG hoặc WEBP - tối đa 5MB
                    </p>

                    <p>
                        Không chọn ảnh mới thì giữ ảnh hiện tại.
                    </p>

                </div>

            </label>

            <input
                type="file"
                id="thumbnail"
                name="thumbnail"
                accept=".jpg,.jpeg,.png,.webp"
                hidden
            >

            <div
                id="imagePreview"
                class="image-preview"
            ></div>

        </div>

        <div class="form-group">

            <label for="content">
                Nội dung
                <span>*</span>
            </label>

            <textarea
                id="content"
                name="content"
                rows="15"
                placeholder="Nhập nội dung bài viết..."
            ><?= htmlspecialchars(
                $values['content']
            ) ?></textarea>

        </div>

        <div class="form-actions">

            <button
                type="submit"
                name="delete_post"
                value="1"
                class="delete-button"
                onclick="
                    return confirm(
                        'Bạn có chắc chắn muốn xóa bài viết này không?'
                    );
                "
            >

                <i class="fa-regular fa-trash-can"></i>

                Xóa bài

            </button>

            <div class="form-actions-right">

                <a
                    href="<?= BASE_URL ?>author/view.php?id=<?= $post['id'] ?>"
                    class="cancel-button"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    name="action"
                    value="draft"
                    class="draft-button"
                >

                    <i class="fa-regular fa-floppy-disk"></i>

                    Lưu nháp

                </button>

                <button
                    type="submit"
                    name="action"
                    value="submit"
                    class="submit-button"
                >

                    <i class="fa-regular fa-paper-plane"></i>

                    Gửi duyệt

                </button>

            </div>

        </div>

    </form>

</div>

<script>
const thumbnailInput =
    document.getElementById('thumbnail');

const imagePreview =
    document.getElementById('imagePreview');

thumbnailInput.addEventListener(
    'change',
    function() {

        const file = this.files[0];

        if (!file) {
            imagePreview.innerHTML = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {

            imagePreview.innerHTML =
                '<p>Ảnh mới</p>' +
                '<img src="' +
                e.target.result +
                '" alt="Ảnh mới">';
        };

        reader.readAsDataURL(file);
    }
);
</script>
