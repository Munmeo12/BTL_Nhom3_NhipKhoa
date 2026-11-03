<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giới thiệu nhóm</title>
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/style.css">
</head>
<body>
    <main class="container">
        <nav>
            <a href="<?= e(BASE_URL) ?>index.php">Trang chủ</a>
            <a href="<?= e(BASE_URL) ?>about.php">Giới thiệu nhóm</a>
            <a href="<?= e(BASE_URL) ?>dang-nhap.php">Đăng nhập</a>
            <a href="<?= e(BASE_URL) ?>pages/tim-kiem.php">Tìm kiếm</a>
        </nav>

        <section class="intro small">
            <h1>Giới thiệu nhóm</h1>
            <p>Nhóm gồm 4 thành viên cùng thực hiện bài tập lớn môn Lập trình web.</p>
        </section>

        <?php $hasMssv = array_filter(array_column($members, 'mssv')) !== []; ?>
        <section class="box">
            <h2>Thành viên</h2>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <?php if ($hasMssv): ?><th>Mã sinh viên</th><?php endif; ?>
                        <th>Họ và tên</th>
                        <th>Vai trò phụ trách</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <?php if ($hasMssv): ?><td><?= e($member['mssv']) ?></td><?php endif; ?>
                            <td><?= e($member['name']) ?></td>
                            <td><?= e($member['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="box">
            <h2>Đề tài</h2>
            <h3>Website tin tức/blog có trang quản trị</h3>
            <p>
                Nhịp Khoa là website tin tức của khoa. Tác giả viết bài, biên tập viên
                duyệt trước khi xuất bản, khách đọc, tìm kiếm và bình luận. Quản trị
                viên quản lý tài khoản, chuyên mục, thẻ và bình luận.
            </p>
        </section>
    </main>
</body>
</html>
