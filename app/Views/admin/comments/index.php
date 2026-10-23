<style>

/* =========================================================
   TRANG QUẢN LÝ BÌNH LUẬN
   ========================================================= */

.comment-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px 50px;
}

.comment-header {
    margin-bottom: 25px;
}

.comment-header h1 {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
}

.comment-header p {
    margin: 0;
    color: #666;
    font-size: 15px;
}


/* =========================================================
   THÔNG BÁO
   ========================================================= */

.message {
    background: #e5f7e9;
    color: #176b2c;
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}


/* =========================================================
   THANH TÌM KIẾM
   ========================================================= */

.comment-search {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.comment-search input[type="text"] {
    width: 320px;
    height: 42px;
    padding: 0 12px;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    background: #fff;
    color: #333;
    font-size: 14px;
    box-sizing: border-box;
    outline: none;
}

.comment-search input[type="text"]:focus {
    border-color: #8b2f25;
    box-shadow: 0 0 0 3px rgba(139, 47, 37, 0.08);
}

.comment-search select {
    width: 180px;
    height: 42px;
    padding: 0 12px;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    background: white;
    color: #333;
    font-size: 14px;
    box-sizing: border-box;
    outline: none;
    cursor: pointer;
}

.comment-search select:focus {
    border-color: #8b2f25;
    box-shadow: 0 0 0 3px rgba(139, 47, 37, 0.08);
}

.comment-search button {
    width: 90px;
    height: 42px;
    border: none;
    border-radius: 6px;
    background: #8b2f25;
    color: white;
    font-size: 14px;
    cursor: pointer;
    flex-shrink: 0;
}

.comment-search button:hover {
    background: #72251e;
}


/* =========================================================
   BẢNG
   ========================================================= */

.comment-table-wrapper {
    background: white;
    border-radius: 12px;
    padding: 20px;
    overflow-x: auto;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.comment-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 950px;
}

.comment-table th,
.comment-table td {
    padding: 14px;
    border-bottom: 1px solid #eee;
    text-align: left;
    vertical-align: top;
}

.comment-table th {
    background: #f5f1ec;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.comment-table td {
    color: #374151;
    font-size: 14px;
}

.comment-table tbody tr:hover {
    background: #fcfaf8;
}

.comment-table tr:last-child td {
    border-bottom: none;
}


/* =========================================================
   NỘI DUNG BÌNH LUẬN
   ========================================================= */

.comment-content {
    max-width: 350px;
    word-break: break-word;
}

.comment-detail-link {
    color: #333;
    text-decoration: none;
}

.comment-detail-link:hover {
    color: #8b2f25;
    text-decoration: underline;
}


/* =========================================================
   TRẠNG THÁI
   ========================================================= */

.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-hidden {
    background: #e2e3e5;
    color: #383d41;
}


/* =========================================================
   NÚT THAO TÁC
   ========================================================= */

.action-button {
    padding: 7px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 2px;
    font-size: 13px;
    font-weight: 500;
    color: #fff;
}

.action-button:hover {
    opacity: 0.9;
}

.btn-duyet {
    background: #198754;
}

.hide-button {
    background: #6c757d;
}

.show-button {
    background: #2563eb;
}

.delete-button {
    background: #dc2626;
}


/* =========================================================
   KHÔNG CÓ DỮ LIỆU
   ========================================================= */

.empty {
    text-align: center !important;
    padding: 35px !important;
    color: #777 !important;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 768px) {

    .comment-page {
        margin: 25px auto;
        padding: 0 15px 40px;
    }

    .comment-header h1 {
        font-size: 24px;
    }

    .comment-search {
        flex-direction: column;
        align-items: stretch;
    }

    .comment-search input[type="text"],
    .comment-search select,
    .comment-search button {
        width: 100%;
    }

}

</style>


<div class="comment-page">

    <!-- =====================================================
         TIÊU ĐỀ
         ===================================================== -->

    <div class="comment-header">

        <h1>Quản lý bình luận</h1>

        <p>
            Quản lý các bình luận của người dùng trên Nhịp Khoa.
        </p>

    </div>


    <!-- =====================================================
         THÔNG BÁO
         ===================================================== -->

    <?php if ($thongBao !== ''): ?>

        <div class="message">

            <?= htmlspecialchars(
                $thongBao,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         TÌM KIẾM + LỌC
         ===================================================== -->

    <form
        method="GET"
        class="comment-search"
    >

        <!-- Từ khóa -->

        <input
            type="text"
            name="keyword"
            placeholder="Tìm theo nội dung, người bình luận hoặc bài viết..."
            value="<?= htmlspecialchars(
                $keyword,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >


        <!-- Trạng thái -->

        <select name="status">

            <option
                value="all"
                <?= $filter === 'all' ? 'selected' : '' ?>
            >
                Tất cả trạng thái
            </option>

            <option
                value="pending"
                <?= $filter === 'pending' ? 'selected' : '' ?>
            >
                Chờ duyệt
            </option>

            <option
                value="approved"
                <?= $filter === 'approved' ? 'selected' : '' ?>
            >
                Đã hiển thị
            </option>

            <option
                value="hidden"
                <?= $filter === 'hidden' ? 'selected' : '' ?>
            >
                Đã ẩn
            </option>

        </select>


        <!-- Bài viết -->

        <select name="post_id">

            <option value="0">
                Tất cả bài viết
            </option>

            <?php foreach ($danhSachBaiViet as $post): ?>

                <option
                    value="<?= (int) $post['id'] ?>"
                    <?= $postId === (int) $post['id']
                        ? 'selected'
                        : '' ?>
                >
                    <?= htmlspecialchars(
                        $post['title'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </option>

            <?php endforeach; ?>

        </select>


        <!-- Nút tìm kiếm -->

        <button type="submit">
            Tìm kiếm
        </button>

    </form>


    <!-- =====================================================
         BẢNG BÌNH LUẬN
         ===================================================== -->

    <div class="comment-table-wrapper">

        <table class="comment-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Người bình luận</th>

                    <th>Bài viết</th>

                    <th>Nội dung</th>

                    <th>Thời gian</th>

                    <th>Trạng thái</th>

                    <th>Thao tác</th>

                </tr>

            </thead>


            <tbody>

            <?php if (count($binhLuan) === 0): ?>

                <tr>

                    <td
                        colspan="7"
                        class="empty"
                    >
                        Không có bình luận nào.
                    </td>

                </tr>

            <?php else: ?>


                <?php foreach ($binhLuan as $item): ?>

                    <tr>


                        <!-- =================================================
                             ID
                             ================================================= -->

                        <td>

                            <?= (int) $item['id'] ?>

                        </td>


                        <!-- =================================================
                             NGƯỜI BÌNH LUẬN
                             ================================================= -->

                        <td>

                            <?= htmlspecialchars(
                                $item['user_name']
                                    ?? 'Không xác định',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- =================================================
                             BÀI VIẾT
                             ================================================= -->

                        <td>

                            <a
                                href="chi-tiet-binh-luan.php?id=<?= (int) $item['id'] ?>"
                                class="comment-detail-link"
                            >

                                <?= htmlspecialchars(
                                    $item['post_title']
                                        ?? 'Bài viết không tồn tại',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </a>

                        </td>


                        <!-- =================================================
                             NỘI DUNG
                             ================================================= -->

                        <td class="comment-content">

                            <a
                                href="chi-tiet-binh-luan.php?id=<?= (int) $item['id'] ?>"
                                class="comment-detail-link"
                            >

                                <?= htmlspecialchars(
                                    $item['content'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </a>

                        </td>


                        <!-- =================================================
                             THỜI GIAN
                             ================================================= -->

                        <td>

                            <?= htmlspecialchars(
                                $item['created_at'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- =================================================
                             TRẠNG THÁI
                             ================================================= -->

                        <td>

                            <?php if (
                                ($item['status'] ?? '') === 'pending'
                            ): ?>

                                <span class="status status-pending">
                                    Chờ duyệt
                                </span>

                            <?php elseif (
                                ($item['status'] ?? '') === 'approved'
                            ): ?>

                                <span class="status status-approved">
                                    Đã hiển thị
                                </span>

                            <?php else: ?>

                                <span class="status status-hidden">
                                    Đã ẩn
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- =================================================
                             THAO TÁC
                             ================================================= -->

                        <td>

                            <?php if (
                                ($item['status'] ?? '') === 'pending'
                            ): ?>


                                <!-- DUYỆT -->

                                <button
                                    type="button"
                                    class="action-button btn-duyet"
                                    data-id="<?= (int) $item['id'] ?>"
                                    data-csrf="<?= htmlspecialchars(
                                        $csrfToken,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    Duyệt
                                </button>


                                <!-- ẨN -->

                                <button
                                    type="button"
                                    class="action-button hide-button"
                                    data-id="<?= (int) $item['id'] ?>"
                                    data-csrf="<?= htmlspecialchars(
                                        $csrfToken,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    Ẩn
                                </button>


                            <?php elseif (
                                ($item['status'] ?? '') === 'approved'
                            ): ?>


                                <!-- ẨN -->

                                <button
                                    type="button"
                                    class="action-button hide-button"
                                    data-id="<?= (int) $item['id'] ?>"
                                    data-csrf="<?= htmlspecialchars(
                                        $csrfToken,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    Ẩn
                                </button>


                            <?php elseif (
                                ($item['status'] ?? '') === 'hidden'
                            ): ?>


                                <!-- HIỂN THỊ -->

                                <button
                                    type="button"
                                    class="action-button show-button"
                                    data-id="<?= (int) $item['id'] ?>"
                                    data-csrf="<?= htmlspecialchars(
                                        $csrfToken,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    Hiển thị
                                </button>


                            <?php endif; ?>


                            <!-- XÓA -->

                            <button
                                type="button"
                                class="action-button delete-button"
                                data-id="<?= (int) $item['id'] ?>"
                                data-csrf="<?= htmlspecialchars(
                                    $csrfToken,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                Xóa
                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script>

/* =========================================================
   HÀM XỬ LÝ CHUNG
   ========================================================= */

function updateCommentStatus(button, action, status, confirmMessage) {

    if (confirmMessage) {

        const confirmed = confirm(confirmMessage);

        if (!confirmed) {
            return;
        }

    }

    const commentId = button.dataset.id;
    const csrfToken = button.dataset.csrf;

    const formData = new FormData();

    formData.append(
        'comment_id',
        commentId
    );

    formData.append(
        'csrf_token',
        csrfToken
    );

    formData.append(
        'status',
        status
    );

    formData.append(
        'action',
        action
    );


    /*
     * binhluan.php nằm trong admin/
     *
     * API nằm trong admin/api/
     *
     * Vì vậy:
     *
     * api/duyet-binh-luan.php
     *
     * là đúng.
     */

    fetch('api/duyet-binh-luan.php', {

        method: 'POST',

        body: formData

    })

    .then(function(response) {

        return response.json();

    })

    .then(function(data) {

        if (data.success) {

            alert(
                data.message
                || 'Thao tác thành công.'
            );

            location.reload();

        } else {

            alert(
                data.message
                || 'Không thể thực hiện thao tác.'
            );

        }

    })

    .catch(function(error) {

        console.error(error);

        alert(
            'Có lỗi xảy ra. Vui lòng thử lại.'
        );

    });

}


/* =========================================================
   DUYỆT BÌNH LUẬN
   ========================================================= */

document
    .querySelectorAll('.btn-duyet')
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {

                updateCommentStatus(
                    this,
                    'approve',
                    'approved',
                    null
                );

            }
        );

    });


/* =========================================================
   ẨN BÌNH LUẬN
   ========================================================= */

document
    .querySelectorAll('.hide-button')
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {

                updateCommentStatus(
                    this,
                    'hide',
                    'hidden',
                    'Bạn có chắc chắn muốn ẩn bình luận này không?'
                );

            }
        );

    });


/* =========================================================
   HIỂN THỊ LẠI
   ========================================================= */

document
    .querySelectorAll('.show-button')
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {

                updateCommentStatus(
                    this,
                    'show',
                    'approved',
                    null
                );

            }
        );

    });


/* =========================================================
   XÓA BÌNH LUẬN
   ========================================================= */

document
    .querySelectorAll('.delete-button')
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {

                updateCommentStatus(
                    this,
                    'delete',
                    '',
                    'Bạn có chắc chắn muốn xóa bình luận này không? Hành động này không thể hoàn tác.'
                );

            }
        );

    });

</script>

