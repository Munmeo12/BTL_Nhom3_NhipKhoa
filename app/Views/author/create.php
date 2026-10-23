<?php declare(strict_types=1); ?>

<div class="create-page-container">

    <div class="create-page-header">
        <h1>Tạo bài viết mới</h1>
        <p>Soạn nội dung bài viết của bạn.</p>
    </div>
<?php if ($success !== ''): ?>
    <div class="form-success">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

    <?php if ($error): ?>
        <div class="form-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="post-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">
            <label for="title">Tiêu đề <span>*</span></label>
            <input type="text" id="title" name="title"
                   value="<?= e($values['title']) ?>"
                   placeholder="Nhập tiêu đề bài viết..." required>
        </div>

        <div class="form-group">
            <label for="category_id">Chuyên mục <span>*</span></label>

            <select id="category_id" name="category_id" required>
                <option value="">-- Chọn chuyên mục --</option>

                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"
                        <?= (string) $values['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label for="summary">Tóm tắt</label>

            <textarea id="summary" name="summary" rows="3"
                      maxlength="300"
                      placeholder="Nhập mô tả ngắn về bài viết..."><?= e($values['summary']) ?></textarea>

            <small>Tối đa 300 ký tự.</small>
        </div>

        <div class="form-group">
            <label for="thumbnail">Ảnh đại diện</label>

            <label class="upload-box" for="thumbnail">
                <i class="fa-regular fa-image"></i>

                <div>
                    <strong>Chọn ảnh đại diện</strong>
                    <p>JPG, PNG hoặc WEBP - tối đa 5MB</p>
                </div>
            </label>

            <input type="file" id="thumbnail" name="thumbnail"
                   accept=".jpg,.jpeg,.png,.webp" hidden>

            <div id="imagePreview" class="image-preview"></div>
        </div>

        <div class="form-group">
            <label for="content">Nội dung <span>*</span></label>

            <textarea id="content" name="content" rows="15"
                      placeholder="Nhập nội dung bài viết..."
                      required><?= e($values['content']) ?></textarea>
        </div>

        <div class="form-actions">

            <a href="<?= BASE_URL ?>author/posts.php" class="cancel-button">
                Hủy
            </a>

            <button type="submit" name="action" value="draft" class="draft-button">
                <i class="fa-regular fa-floppy-disk"></i>
                Lưu nháp
            </button>

            <button type="submit" name="action" value="submit" class="submit-button">
                <i class="fa-regular fa-paper-plane"></i>
                Gửi duyệt
            </button>

        </div>

    </form>

</div>

<script>
const input = document.getElementById('thumbnail');
const preview = document.getElementById('imagePreview');

input.addEventListener('change', function() {
    const file = this.files[0];

    if (!file) {
        preview.innerHTML = '';
        return;
    }

    const reader = new FileReader();

    reader.onload = function(e) {
        preview.innerHTML =
            '<img src="' + e.target.result + '" alt="Ảnh đại diện">';
    };

    reader.readAsDataURL(file);
});

<?php if ($success !== ''): ?>
input.value = '';
preview.innerHTML = '';
<?php endif; ?>
</script>

