<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chi tiết bình luận</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

       .container {
    max-width: 1100px;
    margin: 20px auto;
    padding: 0 15px;
}

        .back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #333;
        }

        .back:hover {
            text-decoration: underline;
        }

        .page-title {
            margin-bottom: 25px;
        }

        .layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
}

        .card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 2px;
    padding: 14px;
    margin-bottom: 12px;
}
        .card h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .comment-box {
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 18px;
            line-height: 1.7;
            min-height: 120px;
        }

        .post-box {
            display: flex;
            gap: 20px;
        }

        .post-image {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            background: #eee;
        }

        .post-info {
            flex: 1;
        }

        .post-title {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-size: 13px;
            color: #777;
        }

        .info-value {
            font-weight: 600;
        }

        .status {
            display: inline-block;
            width: fit-content;
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .status-hidden {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
     .current-status-card {
    padding: 15px 20px;
}

.current-status {
    display: flex;
    align-items: center;
    gap: 12px;
}

.status-label {
    font-size: 14px;
    color: #555;
}
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn {
    width: 100%;
    padding: 11px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
}

.btn-approve {
    background: #28a745;
    color: white;
}

.btn-hide {
    background: #f0ad4e;
    color: white;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}
.history-item {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.history-item:last-child {
    border-bottom: none;
}

.history-title {
    display: flex;
    align-items: center;
    gap: 7px;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 7px;
}

.history-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.history-dot.pending {
    background: #f0ad4e;
}

.history-dot.sent {
    background: #ddd;
    border: 1px solid #ccc;
}

.history-info {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #777;
    padding-left: 17px;
}
        @media (max-width: 768px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .post-box {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a href="binhluan.php" class="back">
        ← Quay lại quản lý bình luận
    </a>

    <h1 class="page-title">
        Chi tiết bình luận
    </h1>
    <div class="card">

    <div class="current-status">

        <span class="status-label">
            Trạng thái hiện tại:
        </span>

        <?php if ($comment['status'] === 'hidden'): ?>

            <span class="status status-hidden">
                Đã ẩn
            </span>

        <?php elseif ($comment['status'] === 'approved'): ?>

            <span class="status status-approved">
                Đã hiển thị
            </span>

        <?php else: ?>

            <span class="status status-pending">
                Chờ duyệt
            </span>

        <?php endif; ?>

    </div>

</div>

    <div class="layout">

        <!-- CỘT TRÁI -->
        <div>

            <!-- THÔNG TIN BÌNH LUẬN -->
            <div class="card">

                <h2>Nội dung bình luận</h2>

                <div class="comment-box">

                    <?= nl2br(
                        htmlspecialchars(
                            $comment['content'],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ) ?>

                </div>

            </div>


            <!-- THÔNG TIN BÀI VIẾT -->
            <div class="card">

                <h2>Bài viết được bình luận</h2>

                <div class="post-box">

                    <?php if (!empty($comment['post_thumbnail'])): ?>

                        <img
                            src="<?= htmlspecialchars(
                                publicPostImage($comment['post_thumbnail'], BASE_URL),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="post-image"
                            alt="Ảnh bài viết"
                        >

                    <?php else: ?>

                        <div class="post-image"></div>

                    <?php endif; ?>


                    <div class="post-info">

                        <h3 class="post-title">

                            <?= htmlspecialchars(
                                $comment['post_title'] ?? 'Bài viết không tồn tại',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </h3>

                        <div class="info-row">

                            <span class="info-label">
                                ID bài viết
                            </span>

                            <span class="info-value">
                                <?= (int) $comment['post_id'] ?>
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Ngày đăng
                            </span>

                            <span class="info-value">
                                <?= htmlspecialchars(
                                    $comment['post_created_at'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- CỘT PHẢI -->
        <div>

            


            <!-- NGƯỜI BÌNH LUẬN -->
            <div class="card">

                <h2>Người bình luận</h2>

                <div class="info-row">

                    <span class="info-label">
                        Họ và tên
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $comment['user_name'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Email
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $comment['user_email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Thời gian bình luận
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $comment['comment_created_at'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>
            <div class="card">

    <h2>Thao tác</h2>

    <div class="action-buttons">

        <?php if ($comment['status'] !== 'approved'): ?>

            <button
    type="button"
    class="btn btn-approve"
    onclick="updateCommentStatus(<?= (int) $comment['id'] ?>, 'approved')"
>
    ✓ Hiển thị bình luận
</button>

        <?php endif; ?>


        <?php if ($comment['status'] !== 'hidden'): ?>

            <button
                type="button"
                class="btn btn-hide"
                onclick="updateCommentStatus(<?= (int) $comment['id'] ?>, 'hidden')"
            >
                ● Ẩn bình luận
            </button>

        <?php endif; ?>


        <button
            type="button"
            class="btn btn-delete"
            onclick="deleteComment(<?= (int) $comment['id'] ?>)"
        >
            × Xóa bình luận
        </button>

    </div>

</div>

    </div>


    

    


    <!-- ID -->
<div class="card">

    <h2>Thông tin</h2>

    <div class="info-row">

        <span class="info-label">
            ID bình luận
        </span>

        <span class="info-value">
            #<?= (int) $comment['id'] ?>
        </span>

    </div>

</div>

    </div>

</div>
<script>

function updateCommentStatus(commentId, status) {

    let action = '';

    if (status === 'approved') {
        action = 'approve';
    } else if (status === 'hidden') {
        action = 'hide';
    }

   const message = status === 'approved'
    ? 'Bạn có chắc muốn hiển thị bình luận này?'
    : 'Bạn có chắc muốn ẩn bình luận này?';

    if (!confirm(message)) {
        return;
    }

    const formData = new URLSearchParams();

    formData.append(
        'csrf_token',
        '<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>'
    );

    formData.append(
        'comment_id',
        commentId
    );

    formData.append(
        'status',
        status
    );

    formData.append(
        'action',
        action
    );

    fetch('api/duyet-binh-luan.php', {

        method: 'POST',

        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },

        body: formData.toString()

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            alert(data.message);

            location.reload();

        } else {

            alert(
                data.message ||
                'Không thể cập nhật bình luận.'
            );

        }

    })

    .catch(error => {

        console.error(error);

        alert(
            'Có lỗi xảy ra khi kết nối đến máy chủ.'
        );

    });
}


function deleteComment(commentId) {

    if (!confirm(
        'Bạn có chắc chắn muốn xóa bình luận này không?'
    )) {
        return;
    }

    const formData = new URLSearchParams();

    formData.append(
        'csrf_token',
        '<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>'
    );

    formData.append(
        'comment_id',
        commentId
    );

    formData.append(
        'action',
        'delete'
    );

    fetch('api/duyet-binh-luan.php', {

        method: 'POST',

        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },

        body: formData.toString()

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            alert(data.message);

            window.location.href = 'binhluan.php';

        } else {

            alert(
                data.message ||
                'Không thể xóa bình luận.'
            );

        }

    })

    .catch(error => {

        console.error(error);

        alert(
            'Có lỗi xảy ra khi kết nối đến máy chủ.'
        );

    });

}

</script>
</body>
</html>
