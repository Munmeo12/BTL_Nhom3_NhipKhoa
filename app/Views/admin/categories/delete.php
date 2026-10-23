<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Xóa danh mục</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
        }

        .breadcrumb {
            margin-bottom: 28px;
            font-size: 14px;
            color: #64748b;
        }

        .breadcrumb a {
            color: #2563eb;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        

        .page-header {
            margin-bottom: 28px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            font-size: 28px;
            color: #0f172a;
        }

        .page-header p {
            margin: 0;
            color: #64748b;
            font-size: 15px;
        }

        .delete-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 30px;
        }

        .warning-box {
            padding: 16px 18px;
            margin-bottom: 25px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            color: #9a3412;
            font-size: 14px;
            line-height: 1.5;
        }

        .warning-box strong {
            display: block;
            margin-bottom: 5px;
        }

        .category-info {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            padding: 15px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 180px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .info-value {
            flex: 1;
            font-size: 14px;
            color: #1e293b;
        }

        .danger-warning {
            padding: 15px 18px;
            margin-bottom: 25px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #b91c1c;
            font-size: 14px;
            line-height: 1.5;
        }

        .message {
            margin-bottom: 20px;
            padding: 12px 15px;
            border-radius: 6px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            height: 42px;
            padding: 0 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-cancel {
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-cancel:hover {
            background: #f8fafc;
        }

        .btn-delete {
    background: #991b1b;
    color: #ffffff;
    border: 1px solid #991b1b;
}

.btn-delete:hover {
    background: #7f1d1d;
}

        .btn-back {
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-back:hover {
            background: #f8fafc;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="breadcrumb">

        <a href="categories.php">
            Quản lý danh mục
        </a>

        <span> &gt; </span>

        <span>Xóa danh mục</span>

    </div>



    <div class="page-header">

        <h1>Xóa danh mục</h1>

        <p>
            Xác nhận xóa danh mục khỏi hệ thống.
        </p>

    </div>


    <?php if ($message !== ''): ?>

        <div class="message">

            <?= htmlspecialchars(
                $message,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    <?php endif; ?>


    <div class="delete-card">


        <div class="warning-box">

            <strong>
                ⚠️ Bạn có chắc chắn muốn xóa danh mục này?
            </strong>

            Hành động này không thể hoàn tác.

        </div>


        <div class="category-info">


            <div class="info-row">

                <div class="info-label">
                    Tên danh mục
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $category['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            </div>


            <div class="info-row">

                <div class="info-label">
                    Slug
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $category['slug'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            </div>


            <div class="info-row">

                <div class="info-label">
                    Số bài viết
                </div>

                <div class="info-value">

                    <?= (int)$category['post_count'] ?>

                </div>

            </div>


            <div class="info-row">

                <div class="info-label">
                    Ngày tạo
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $category['created_at'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            </div>


        </div>


        <?php if ((int)$category['post_count'] > 0): ?>


            <div class="danger-warning">

                ⚠️ Danh mục này đang chứa
                <strong>
                    <?= (int)$category['post_count'] ?>
                </strong>
                bài viết.

                <br>

                Không thể xóa danh mục đang có bài viết.
                Vui lòng chuyển các bài viết sang danh mục khác
                trước khi xóa.

            </div>


            <div class="form-actions">

                <a
                    href="categories.php"
                    class="btn btn-back"
                >
                    Quay lại
                </a>

            </div>


        <?php else: ?>


            <div class="danger-warning">

                ⚠️ Sau khi xóa, danh mục này sẽ không thể
                khôi phục.

            </div>


            <form method="POST">
            <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
    >
                <div class="form-actions">

                    <a
                        href="categories.php"
                        class="btn btn-cancel"
                    >
                        Hủy bỏ
                    </a>

                    <button
                        type="submit"
                        class="btn btn-delete"
                    >
                        Xóa danh mục
                    </button>

                </div>

            </form>


        <?php endif; ?>


    </div>

</div>

</body>

</html>
