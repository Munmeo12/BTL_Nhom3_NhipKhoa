<style>

/* ============================================================
   TRANG QUẢN LÝ DANH MỤC
   ============================================================ */

.category-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px 50px;
}


/* ============================================================
   TIÊU ĐỀ
   ============================================================ */

.category-header {
    margin-bottom: 25px;
}

.category-header h1 {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
}

.category-header p {
    margin: 0;
    color: #6b7280;
    font-size: 15px;
}


/* ============================================================
   THANH TÌM KIẾM
   ============================================================ */

.category-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.category-search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    flex-wrap: wrap;
}

.category-search-box input,
.category-search-box select {
    height: 42px;
    padding: 0 14px;
    border: 1px solid #ddd8d2;
    border-radius: 7px;
    background: #ffffff;
    color: #374151;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
}

.category-search-box input {
    width: 280px;
}

.category-search-box select {
    width: 190px;
    cursor: pointer;
}

.category-search-box input:focus,
.category-search-box select:focus {
    border-color: #991b1b;
    box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.08);
}


/* ============================================================
   NÚT
   ============================================================ */

.category-search-button,
.category-add-button {
    height: 42px;
    padding: 0 18px;
    border: none;
    border-radius: 7px;
    background: #991b1b;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    box-sizing: border-box;
}

.category-search-button:hover,
.category-add-button:hover {
    background: #7f1d1d;
}


/* ============================================================
   KHUNG BẢNG
   ============================================================ */

.category-table-box {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    overflow-x: auto;
    padding: 20px;
}

.category-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}


/* ============================================================
   HEADER BẢNG
   ============================================================ */

.category-table th {
    background: #f7f4f0;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    text-align: left;
    padding: 15px;
    border-bottom: 1px solid #e5e0da;
    white-space: nowrap;
}


/* ============================================================
   NỘI DUNG BẢNG
   ============================================================ */

.category-table td {
    padding: 15px;
    border-bottom: 1px solid #eee9e4;
    font-size: 14px;
    color: #374151;
    vertical-align: middle;
}

.category-table tr:last-child td {
    border-bottom: none;
}

.category-table tbody tr:hover {
    background: #fcfaf8;
}


/* ============================================================
   TRẠNG THÁI
   ============================================================ */

.category-status-active,
.category-status-hidden {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

.category-status-active {
    background: #dcfce7;
    color: #166534;
}

.category-status-hidden {
    background: #f1f5f9;
    color: #475569;
}


/* ============================================================
   THAO TÁC
   ============================================================ */

.category-actions {
    white-space: nowrap;
}

.category-actions a {
    color: #2563eb;
    text-decoration: none;
    margin-right: 10px;
}

.category-actions a:hover {
    text-decoration: underline;
}

.category-actions .delete-link {
    color: #dc2626;
}

.category-actions .delete-link:hover {
    color: #b91c1c;
}

.category-action-separator {
    color: #9ca3af;
    margin-right: 10px;
}


/* ============================================================
   KHÔNG CÓ DỮ LIỆU
   ============================================================ */

.category-empty {
    text-align: center !important;
    color: #64748b !important;
    padding: 35px !important;
}


/* ============================================================
   RESPONSIVE
   ============================================================ */

@media (max-width: 768px) {

    .category-page {
        margin: 25px auto;
        padding: 0 15px 40px;
    }

    .category-top-bar {
        align-items: stretch;
    }

    .category-search-box {
        width: 100%;
    }

    .category-search-box input,
    .category-search-box select,
    .category-search-button {
        width: 100%;
    }

    .category-add-button {
        width: 100%;
    }

    .category-header h1 {
        font-size: 24px;
    }
}

</style>


<!-- ============================================================
     NỘI DUNG QUẢN LÝ DANH MỤC

     KHÔNG thêm <main> ở đây vì header.php
     của bạn đã có <main> và footer.php sẽ đóng </main>.
     ============================================================ -->

<div class="category-page">

    <!-- ========================================================
         TIÊU ĐỀ
         ======================================================== -->

    <div class="category-header">

        <h1>Quản lý danh mục</h1>

        <p>
            Quản lý các danh mục bài viết trong hệ thống.
        </p>

    </div>


    <!-- ========================================================
         THANH TÌM KIẾM
         ======================================================== -->

    <div class="category-top-bar">

        <form
            method="GET"
            class="category-search-box"
        >

            <input
                type="text"
                name="keyword"
                placeholder="Tìm kiếm danh mục..."
                value="<?= htmlspecialchars(
                    $keyword,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <select name="status">

                <option
                    value="all"
                    <?= $status === 'all' ? 'selected' : '' ?>
                >
                    Tất cả trạng thái
                </option>

                <option
                    value="active"
                    <?= $status === 'active' ? 'selected' : '' ?>
                >
                    Hiển thị
                </option>

                <option
                    value="hidden"
                    <?= $status === 'hidden' ? 'selected' : '' ?>
                >
                    Ẩn
                </option>

            </select>

            <button
                type="submit"
                class="category-search-button"
            >
                Tìm kiếm
            </button>

        </form>


        <!-- ====================================================
             THÊM DANH MỤC
             ==================================================== -->

        <a
            href="category-add.php"
            class="category-add-button"
        >
            + Thêm danh mục
        </a>

    </div>


    <!-- ========================================================
         DANH SÁCH DANH MỤC
         ======================================================== -->

    <div class="category-table-box">

        <table class="category-table">

            <thead>

                <tr>

                    <th>STT</th>

                    <th>Tên danh mục</th>

                    <th>Slug</th>

                    <th>Số bài viết</th>

                    <th>Trạng thái</th>

                    <th>Ngày tạo</th>

                    <th>Thao tác</th>

                </tr>

            </thead>


            <tbody>

            <?php if (empty($categories)): ?>

                <tr>

                    <td
                        colspan="7"
                        class="category-empty"
                    >
                        Không có danh mục nào.
                    </td>

                </tr>

            <?php else: ?>

                <?php foreach ($categories as $index => $category): ?>

                    <tr>

                        <!-- =================================================
                             STT
                             ================================================= -->

                        <td>
                            <?= $index + 1 ?>
                        </td>


                        <!-- =================================================
                             TÊN DANH MỤC
                             ================================================= -->

                        <td>
                            <?= htmlspecialchars(
                                $category['name'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>


                        <!-- =================================================
                             SLUG
                             ================================================= -->

                        <td>
                            <?= htmlspecialchars(
                                $category['slug'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>


                        <!-- =================================================
                             SỐ BÀI VIẾT
                             ================================================= -->

                        <td>
                            <?= (int) (
                                $category['post_count'] ?? 0
                            ) ?>
                        </td>


                        <!-- =================================================
                             TRẠNG THÁI
                             ================================================= -->

                        <td>

                            <?php if (
                                ($category['status'] ?? '') === 'active'
                            ): ?>

                                <span class="category-status-active">
                                    Hiển thị
                                </span>

                            <?php else: ?>

                                <span class="category-status-hidden">
                                    Ẩn
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- =================================================
                             NGÀY TẠO
                             ================================================= -->

                        <td>
                            <?= htmlspecialchars(
                                $category['created_at'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>


                        <!-- =================================================
                             THAO TÁC
                             ================================================= -->

                        <td class="category-actions">

                            <a
                                href="category-edit.php?id=<?= (int) $category['id'] ?>"
                            >
                                Sửa
                            </a>

                            <span class="category-action-separator">
                                |
                            </span>

                            <a
                                href="category-delete.php?id=<?= (int) $category['id'] ?>"
                                class="delete-link"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');"
                            >
                                Xóa
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

