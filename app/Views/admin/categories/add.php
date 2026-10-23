<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thêm danh mục</title>

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

        /* Breadcrumb */

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

        /* Header */

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

    

        /* Form */

        .form-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .required {
            color: #dc2626;
        }

        .form-control {
            width: 100%;
            height: 44px;
            padding: 0 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            color: #1e293b;
            font-size: 14px;
            outline: none;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea.form-control {
            height: 120px;
            padding: 12px 13px;
            resize: vertical;
            font-family: Arial, sans-serif;
        }

        select.form-control {
            cursor: pointer;
        }

        .helper-text {
            margin-top: 7px;
            margin-bottom: 0;
            font-size: 13px;
            color: #64748b;
        }

        /* Message */

        .message {
            margin-bottom: 20px;
            padding: 12px 15px;
            border-radius: 6px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 14px;
        }

        /* Buttons */

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
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

        .btn-save {
    background: #991b1b;
    color: #ffffff;
    border: 1px solid #991b1b;
}

.btn-save:hover {
    background: #7f1d1d;
}
    </style>

</head>

<body>

<div class="container">

    <!-- Breadcrumb -->

    <div class="breadcrumb">

        <a href="categories.php">
            Quản lý danh mục
        </a>

        <span> &gt; </span>

        <span>Thêm danh mục</span>

    </div>


    

    <!-- Tiêu đề -->

    <div class="page-header">

        <h1>Thêm danh mục</h1>

        <p>
            Tạo một danh mục mới để phân loại bài viết.
        </p>

    </div>


    <!-- Thông báo lỗi -->

    <?php if ($message !== ''): ?>

        <div class="message">

            <?= htmlspecialchars(
                $message,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    <?php endif; ?>


    <!-- Form -->

    <div class="form-card">

        <form method="POST">

        <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
    >
            <!-- Tên danh mục -->

            <div class="form-group">

                <label for="name">
                    Tên danh mục
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="Nhập tên danh mục"
                    value="<?= htmlspecialchars(
                        $name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

            </div>


            <!-- Mô tả -->

            <div class="form-group">

                <label for="description">
                    Mô tả
                </label>

                <textarea
                    id="description"
                    name="description"
                    class="form-control"
                    placeholder="Nhập mô tả cho danh mục..."
                ><?= htmlspecialchars(
                    $description,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>

            </div>


            <!-- Slug -->

            <div class="form-group">

                <label for="slug">
                    Slug
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="slug"
                    name="slug"
                    class="form-control"
                    placeholder="vi-du-slug"
                    value="<?= htmlspecialchars(
                        $slug,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

                <p class="helper-text">
                    Sử dụng đường URL thân thiện cho danh mục.
                </p>

            </div>


            <!-- Trạng thái -->

            <div class="form-group">

                <label for="status">
                    Trạng thái
                </label>

                <select
                    id="status"
                    name="status"
                    class="form-control"
                >

                    <option
                        value="active"
                        <?= $status === 'active'
                            ? 'selected'
                            : '' ?>
                    >
                        Hiển thị
                    </option>

                    <option
                        value="hidden"
                        <?= $status === 'hidden'
                            ? 'selected'
                            : '' ?>
                    >
                        Ẩn
                    </option>

                </select>

            </div>


            <!-- Nút -->

            <div class="form-actions">

                <a
                    href="categories.php"
                    class="btn btn-cancel"
                >
                    Hủy bỏ
                </a>

                <button
                    type="submit"
                    class="btn btn-save"
                >
                    Lưu danh mục
                </button>

            </div>


        </form>

    </div>

</div>

</body>

</html>
