<?php
/** @var array $profile */
/** @var array $errors */
/** @var string $notice */
/** @var string $nameValue */
/** @var string $role */
$roleNames = ['reader' => 'Độc giả', 'author' => 'Tác giả', 'editor' => 'Biên tập viên'];
$hasAvatar = avatarUrl($profile['avatar'] ?? null) !== null;
$field = static fn(string $key): string => isset($errors[$key]) ? '<p class="pf-error" role="alert">' . e($errors[$key]) . '</p>' : '';
?>
<div class="pf-wrap">
    <header class="pf-head">
        <p class="pf-kicker">TÀI KHOẢN</p>
        <h1>Trang cá nhân</h1>
        <p>Cập nhật tên hiển thị, mật khẩu và ảnh đại diện của bạn.</p>
    </header>

    <?php if ($notice !== ''): ?><div class="pf-alert pf-success" role="status"><?= e($notice) ?></div><?php endif; ?>
    <?php if (isset($errors['form'])): ?><div class="pf-alert pf-danger" role="alert"><?= e($errors['form']) ?></div><?php endif; ?>

    <section class="pf-card pf-identity">
        <?= avatarTag((string) $profile['full_name'], $profile['avatar'] ?? null, 'lg') ?>
        <div>
            <h2><?= e($profile['full_name']) ?></h2>
            <p><?= e($profile['email']) ?></p>
            <span class="pf-role"><?= e($roleNames[$role] ?? $role) ?></span>
            <?php if (!empty($profile['created_at'])): ?><small>Tham gia từ <?= e(date('d/m/Y', strtotime((string) $profile['created_at']))) ?></small><?php endif; ?>
        </div>
    </section>

    <section class="pf-card">
        <h2>Ảnh đại diện</h2>
        <p class="pf-hint">JPG, PNG hoặc WEBP, tối đa 2MB. Ảnh sẽ hiển thị ở bình luận, bài viết và các trang quản lý.</p>
        <form method="post" enctype="multipart/form-data" class="pf-avatar-form" id="avatarForm">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="update_avatar">
            <div class="pf-avatar-preview" id="avatarPreview"><?= avatarInner((string) $profile['full_name'], $profile['avatar'] ?? null) ?></div>
            <div class="pf-avatar-controls">
                <label class="pf-file">
                    <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/webp" required>
                    <span>Chọn ảnh mới</span>
                </label>
                <button type="submit" class="pf-btn">Lưu ảnh</button>
            </div>
            <?= $field('avatar') ?>
            <p class="pf-error" id="avatarClientError" role="alert" hidden></p>
        </form>
        <?php if ($hasAvatar): ?>
            <form method="post" class="pf-inline" onsubmit="return confirm('Xóa ảnh đại diện hiện tại?');">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="remove_avatar">
                <button type="submit" class="pf-btn pf-btn-ghost">Xóa ảnh đại diện</button>
            </form>
        <?php endif; ?>
    </section>

    <section class="pf-card">
        <h2>Tên hiển thị</h2>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="update_name">
            <label for="pfName">Họ và tên</label>
            <input id="pfName" type="text" name="full_name" value="<?= e($nameValue) ?>" maxlength="120" required autocomplete="name">
            <?= $field('full_name') ?>
            <button type="submit" class="pf-btn">Lưu tên</button>
        </form>
    </section>

    <section class="pf-card">
        <h2>Đổi mật khẩu</h2>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="change_password">
            <label for="pfCurrent">Mật khẩu hiện tại</label>
            <input id="pfCurrent" type="password" name="current_password" autocomplete="current-password" required>
            <?= $field('current_password') ?>
            <label for="pfNew">Mật khẩu mới</label>
            <input id="pfNew" type="password" name="new_password" autocomplete="new-password" minlength="8" maxlength="72" required>
            <p class="pf-hint">8–72 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
            <?= $field('new_password') ?>
            <label for="pfConfirm">Nhập lại mật khẩu mới</label>
            <input id="pfConfirm" type="password" name="new_password_confirmation" autocomplete="new-password" required>
            <?= $field('new_password_confirmation') ?>
            <button type="submit" class="pf-btn">Đổi mật khẩu</button>
        </form>
    </section>
</div>
<script>
(function () {
    var input = document.getElementById('avatarInput');
    if (!input) return;
    var preview = document.getElementById('avatarPreview');
    var error = document.getElementById('avatarClientError');
    var form = document.getElementById('avatarForm');
    var allowed = ['image/jpeg', 'image/png', 'image/webp'];
    var maxBytes = 2 * 1024 * 1024;
    var original = preview.innerHTML;
    input.addEventListener('change', function () {
        error.hidden = true;
        error.textContent = '';
        var file = input.files && input.files[0];
        if (!file) { preview.innerHTML = original; return; }
        var message = '';
        if (allowed.indexOf(file.type) === -1) { message = 'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.'; }
        else if (file.size > maxBytes) { message = 'Ảnh không được lớn hơn 2MB.'; }
        if (message !== '') {
            error.textContent = message;
            error.hidden = false;
            input.value = '';
            preview.innerHTML = original;
            return;
        }
        var img = document.createElement('img');
        img.alt = '';
        img.src = URL.createObjectURL(file);
        preview.textContent = '';
        preview.appendChild(img);
    });
    form.addEventListener('submit', function (event) {
        if (!input.files || !input.files[0]) { event.preventDefault(); }
    });
})();
</script>
