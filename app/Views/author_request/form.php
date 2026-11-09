<?php
declare(strict_types=1);
/** @var array $values */
/** @var array $errors */
/** @var ?array $latest */
/** @var array $history */
/** @var string $databaseError */
/** @var string $notice */
/** @var string $role */
/** @var string $roleName */

$statusLabels = ['pending' => 'Đang chờ duyệt', 'approved' => 'Đã được duyệt', 'rejected' => 'Bị từ chối', 'revoked' => 'Đã bị thu hồi'];
// Đã duyệt nhưng hiện lại là Độc giả nghĩa là Admin đã thu hồi quyền sau đó.
$revoked = $latest !== null && $latest['status'] === 'approved' && $role === 'reader';
$latestStatus = $revoked ? 'revoked' : (string) ($latest['status'] ?? '');
$hasPending = $latest !== null && $latest['status'] === 'pending';
$canSubmit = $role === 'reader' && !$hasPending && $databaseError === '';
$landing = (new \App\Services\AuthService())->landingPage($role);
$dateFormat = static fn(?string $value): string => $value ? date('d/m/Y H:i', strtotime($value)) : '';
?>
<style>
.ar-wrap{max-width:820px;margin:0 auto;padding:38px 20px 60px}
.ar-heading h1{margin:0 0 8px;font:700 28px Lora,serif;color:#142035}
.ar-heading p{margin:0 0 24px;color:#49576e;font-size:14px;line-height:1.6}
.ar-card{background:#fff;border:1px solid #dce0e7;border-radius:12px;padding:24px;margin-bottom:20px;box-shadow:0 8px 28px rgba(20,32,53,.045)}
.ar-card h2{margin:0 0 14px;font:600 18px Lora,serif;color:#142035}
.ar-alert{padding:12px 15px;border-radius:8px;font-size:13px;margin-bottom:18px}
.ar-alert.success{color:#286448;background:#e8f5ee;border:1px solid #b9ddc9}
.ar-alert.error{color:#8b3330;background:#fae7e6;border:1px solid #ebc1be}
.ar-alert.info{color:#6e4f1b;background:#fff3d8;border:1px solid #f0d9a5}
.ar-field{margin-bottom:18px}
.ar-field label{display:block;margin-bottom:6px;font-size:13px;font-weight:600;color:#0e1625}
.ar-field label span{color:#1b3a6e}
.ar-field input[type=text],.ar-field input[type=url],.ar-field textarea{width:100%;padding:11px 12px;border:1px solid #ccd1db;border-radius:8px;font:14px Inter,Arial,sans-serif;color:#0e1625;background:#fff;outline:0}
.ar-field textarea{min-height:140px;resize:vertical;line-height:1.6}
.ar-field input:focus,.ar-field textarea:focus{border-color:#1b3a6e;box-shadow:0 0 0 3px rgba(27,58,110,.1)}
.ar-field small{display:block;margin-top:5px;color:#747f92;font-size:12px}
.ar-field .ar-error{color:#8b3330;font-weight:500}
.ar-check{display:flex;gap:10px;align-items:flex-start;font-size:13px;line-height:1.5;color:#0e1625}
.ar-check input{margin-top:3px}
.ar-btn{display:inline-flex;align-items:center;justify-content:center;height:42px;padding:0 22px;border:0;border-radius:8px;background:#1b3a6e;color:#fff;font:600 14px Inter,Arial,sans-serif;cursor:pointer;text-decoration:none}
.ar-btn:hover{background:#102343}
.ar-chip{display:inline-flex;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600}
.ar-chip.pending{color:#6e4f1b;background:#fff3d8}
.ar-chip.approved{color:#286448;background:#e8f5ee}
.ar-chip.rejected{color:#8b3330;background:#fae7e6}
.ar-chip.revoked{color:#5b6475;background:#eceef2}
.ar-meta{display:grid;grid-template-columns:130px 1fr;gap:8px 14px;margin:14px 0 0;font-size:13px;line-height:1.6}
.ar-meta dt{color:#747f92}
.ar-meta dd{margin:0;color:#0e1625;word-break:break-word}
.ar-note{margin-top:14px;padding:12px 14px;border-left:3px solid #1b3a6e;background:#f6f7fa;font-size:13px;line-height:1.6;color:#142035}
.ar-history{list-style:none;margin:0;padding:0}
.ar-history li{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:11px 0;border-bottom:1px solid #e8eaee;font-size:13px}
.ar-history li:last-child{border-bottom:0}
.ar-history small{color:#747f92}
@media(max-width:600px){.ar-meta{grid-template-columns:1fr}}
</style>

<div class="ar-wrap">
    <div class="ar-heading">
        <h1>Đăng ký làm tác giả</h1>
        <p>Tác giả có thể viết bài, lưu nháp và gửi cho biên tập viên duyệt. Hãy cho chúng tôi biết bạn muốn viết về điều gì để quản trị viên xem xét.</p>
    </div>

    <?php if ($notice !== ''): ?><div class="ar-alert success"><?= e($notice) ?></div><?php endif; ?>
    <?php if ($databaseError !== ''): ?><div class="ar-alert error"><?= e($databaseError) ?></div><?php endif; ?>

    <?php if ($role !== 'reader'): ?>
        <div class="ar-card">
            <h2>Bạn đã có quyền cao hơn Độc giả</h2>
            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#142035">Vai trò hiện tại của bạn là <strong><?= e($roleName) ?></strong>, không cần gửi thêm yêu cầu.</p>
            <?php if ($landing !== 'index.php'): ?><a class="ar-btn" href="<?= e(BASE_URL . $landing) ?>">Đến khu vực của tôi</a><?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($latest !== null): ?>
        <div class="ar-card">
            <h2>Yêu cầu gần nhất <span class="ar-chip <?= e($latestStatus) ?>"><?= e($statusLabels[$latestStatus] ?? $latestStatus) ?></span></h2>
            <dl class="ar-meta">
                <dt>Gửi lúc</dt><dd><?= e($dateFormat($latest['created_at'])) ?></dd>
                <dt>Chủ đề muốn viết</dt><dd><?= e($latest['topics']) ?></dd>
                <dt>Lý do</dt><dd><?= nl2br(e($latest['reason'])) ?></dd>
                <?php if (!empty($latest['sample_link'])): ?><dt>Link bài mẫu</dt><dd><?= e($latest['sample_link']) ?></dd><?php endif; ?>
                <?php if ($latest['reviewed_at']): ?><dt>Xử lý lúc</dt><dd><?= e($dateFormat($latest['reviewed_at'])) ?></dd><?php endif; ?>
            </dl>
            <?php if ($latest['status'] === 'pending'): ?>
                <div class="ar-note">Yêu cầu của bạn đang chờ quản trị viên xem xét. Bạn không cần gửi lại.</div>
            <?php elseif ($latest['status'] === 'rejected' && !empty($latest['admin_note'])): ?>
                <div class="ar-note"><strong>Lý do từ chối:</strong> <?= nl2br(e($latest['admin_note'])) ?><br>Bạn có thể chỉnh lại và gửi yêu cầu mới ở bên dưới.</div>
            <?php elseif ($revoked): ?>
                <div class="ar-note">Quyền tác giả của bạn đã bị quản trị viên thu hồi, vai trò hiện tại là Độc giả. Bạn có thể gửi yêu cầu mới ở bên dưới.</div>
            <?php elseif ($latest['status'] === 'approved'): ?>
                <div class="ar-note">Yêu cầu đã được duyệt.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($canSubmit): ?>
        <div class="ar-card">
            <h2>Gửi yêu cầu mới</h2>
            <?php if (isset($errors['form'])): ?><div class="ar-alert error"><?= e($errors['form']) ?></div><?php endif; ?>
            <form method="post" action="<?= e(BASE_URL) ?>yeu-cau-tac-gia.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                <div class="ar-field">
                    <label for="topics">Chủ đề bạn muốn viết <span>*</span></label>
                    <input type="text" id="topics" name="topics" maxlength="255" value="<?= e($values['topics']) ?>" placeholder="Ví dụ: Học bổng, thực tập, kinh nghiệm học lập trình">
                    <?php if (isset($errors['topics'])): ?><small class="ar-error"><?= e($errors['topics']) ?></small><?php endif; ?>
                </div>

                <div class="ar-field">
                    <label for="reason">Vì sao bạn muốn trở thành tác giả? <span>*</span></label>
                    <textarea id="reason" name="reason" maxlength="1000" placeholder="Giới thiệu ngắn về bạn, kinh nghiệm và nội dung bạn muốn chia sẻ (tối thiểu 30 ký tự)."><?= e($values['reason']) ?></textarea>
                    <?php if (isset($errors['reason'])): ?><small class="ar-error"><?= e($errors['reason']) ?></small><?php else: ?><small>Từ 30 đến 1000 ký tự.</small><?php endif; ?>
                </div>

                <div class="ar-field">
                    <label for="sample_link">Link bài viết mẫu hoặc sản phẩm đã làm</label>
                    <input type="url" id="sample_link" name="sample_link" maxlength="255" value="<?= e($values['sample_link']) ?>" placeholder="https://...">
                    <?php if (isset($errors['sample_link'])): ?><small class="ar-error"><?= e($errors['sample_link']) ?></small><?php else: ?><small>Không bắt buộc, nhưng giúp quản trị viên đánh giá nhanh hơn.</small><?php endif; ?>
                </div>

                <div class="ar-field">
                    <label for="contact">Thông tin liên hệ thêm (Zalo/số điện thoại)</label>
                    <input type="text" id="contact" name="contact" maxlength="100" value="<?= e($values['contact']) ?>" placeholder="Không bắt buộc">
                    <?php if (isset($errors['contact'])): ?><small class="ar-error"><?= e($errors['contact']) ?></small><?php endif; ?>
                </div>

                <div class="ar-field">
                    <label class="ar-check"><input type="checkbox" name="agree" value="1" <?= $values['agree'] === '1' ? 'checked' : '' ?>><span style="color:#0e1625;font-weight:400">Tôi cam kết nội dung đăng lên do chính mình viết và chịu trách nhiệm về nội dung đó.</span></label>
                    <?php if (isset($errors['agree'])): ?><small class="ar-error"><?= e($errors['agree']) ?></small><?php endif; ?>
                </div>

                <button class="ar-btn" type="submit">Gửi yêu cầu</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (count($history) > 1): ?>
        <div class="ar-card">
            <h2>Lịch sử yêu cầu</h2>
            <ul class="ar-history">
                <?php $seenApproved = false; foreach ($history as $item):
                    $itemStatus = (string) $item['status'];
                    if ($itemStatus === 'approved') { if (!$seenApproved && $role === 'reader') { $itemStatus = 'revoked'; } $seenApproved = true; }
                ?>
                    <li>
                        <span><?= e($item['topics']) ?><br><small><?= e($dateFormat($item['created_at'])) ?></small></span>
                        <span class="ar-chip <?= e($itemStatus) ?>"><?= e($statusLabels[$itemStatus] ?? $itemStatus) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
