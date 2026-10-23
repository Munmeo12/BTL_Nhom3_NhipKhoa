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

        <section class="box">
            <h2>Thành viên</h2>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã sinh viên</th>
                        <th>Họ và tên</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= e($member['mssv']) ?></td>
                            <td><?= e($member['name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="box">
            <h2>Đề tài dự kiến</h2>
            <h3>Website tin tức/blog có trang quản trị</h3>
            <p>
                Nhóm dự kiến làm website để đăng và quản lý các bài viết. Người
                dùng có thể xem, tìm kiếm bài viết. Quản trị viên có thể đăng nhập
                để thêm, sửa, xóa bài viết và danh mục.
            </p>
        </section>
    </main>
</body>
</html>
