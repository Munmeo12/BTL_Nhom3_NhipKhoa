# Phân công và bản đồ code theo vai trò

Mỗi thành viên phụ trách một vai trò. Khi báo cáo, mỗi người giải thích được ít nhất một luồng
**route → controller → model → view** trong phần của mình (tiêu chí của đề).

## Phần chung cả nhóm 

Điểm vào mỏng (`*.php`) → `bootstrap/app.php` → Controller (`requireRole`, `verifyCsrf`) → Model (SQL) →
`View::render` trong layout. Vòng đời bài: `draft → pending → published | rejected`. Ba lớp bảo vệ:
prepared statement, `e()`, CSRF.

## Trần Thị Hà Anh - Admin + nền tảng

- **Controller/Service:** `AuthController`, `AuthService`, `AdminUserController`, `CategoryController`,
  `AdminTagController`, `AuthorRequestController::adminIndex`, `CategoryValidator`.
- **Model:** `User`, `Category`, `Tag`, `AuthorRequest`.
- **Nền tảng:** `bootstrap/app.php`, `app/Core/*`, `config/database.php`.
- **View/điểm vào:** `app/Views/auth/*`, `admin/dashboard|users|the|yeu-cau-tac-gia.php`, `views/category*.php`,
  `app/Views/admin/{dashboard,users,categories,tags,author_requests}`, `layouts/admin|auth`, `includes/panel-*.php`.
- **Cần giải thích:** `password_hash/verify`, session, `requireRole`, vì sao role gán ở server, khóa tài khoản
  có hiệu lực ngay, CSRF, CRUD chuyên mục và thẻ, duyệt yêu cầu làm tác giả bằng transaction.

## Nguyễn Hải Vân - Biên tập viên

- **Controller:** `EditorPostController` (duyệt bài, cho Biên tập viên và Admin); `CommentController`
  (kiểm duyệt bình luận, **chỉ Admin truy cập được**, Hải Vân phụ trách giải thích code).
- **Model:** `Post` (`editorCounts`, `pendingForEditor`, `forEditor`, `findForReview`, `review`,
  `unpublish`, `deleteAny`, `setFeatured`), `Comment`.
- **View/điểm vào:** `editor/*.php`, `app/Views/editor/*`, `app/Views/admin/posts.php`,
  `admin/binhluan.php`, `admin/chi-tiet-binh-luan.php`, `admin/api/duyet-binh-luan.php`, `layouts/editor`.
- **Cần giải thích:** vòng đời bài, `UPDATE ... WHERE status = 'pending'` chống duyệt đè,
  hẹn giờ xuất bản (`published_at`), bài nổi bật, endpoint JSON và `fetch()` duyệt bình luận.
- **Lưu ý demo:** thao tác duyệt bình luận trên giao diện làm bằng tài khoản Admin.

## Đào Khánh Trung - Tác giả

- **Controller/Service:** `AuthorPostController`, `AuthorRequestController::form`, `ImageUploadService`, `Slugger`.
- **Model:** `Post` (`authorCounts`, `byAuthor`, `findOwned`, `create`, `updateOwned`, `deleteOwned`,
  `slugExists`), `Tag::sync`, `AuthorRequest` (phía độc giả gửi yêu cầu).
- **View/điểm vào:** `author/*.php`, `app/Views/author/*`, `yeu-cau-tac-gia.php`, `app/Views/author_request/*`, `layouts/author`.
- **Cần giải thích:** tác giả chỉ sửa bài của mình (`WHERE author_id = ?`), chỉ sửa khi nháp/bị từ chối,
  kiểm tra upload bằng MIME thật, slug không trùng, gắn thẻ bằng transaction, trạng thái "Hẹn đăng".

## Nguyễn Trung Vũ Học - Độc giả / Khách

- **Controller:** `HomeController`, `PublicPostController` (danh mục, tìm kiếm, thẻ, RSS),
  `PublicArticleController` (chi tiết, gửi bình luận), `PublicPageController`, `ImpactBoxController`.
- **Model:** `Post` (`publishedForHome`, `publishedList`, `findPublished`, `relatedPublished`,
  `incrementViews`), `Comment` (`approvedForPost`, `createForRole`: bình luận của Admin được duyệt ngay, vai trò khác chờ duyệt), `ImpactBox`.
- **View/điểm vào:** `index.php`, `bai-viet.php`, `pages/*.php`, `rss.php`, `impact-box-action.php`, `views/impact-box.php`,
  `app/Views/public/*`, `app/Views/impact/*`, `layouts/public`, `includes/header|footer|public-posts.php`.
- **Cần giải thích:** vì sao chỉ bài `published` và đã tới giờ mới hiện, tìm kiếm có phân trang,
  bình luận của độc giả ở trạng thái chờ duyệt, Impact Box không lưu trùng (UNIQUE), đếm lượt xem mỗi phiên một lần.

## Phần chung: trang cá nhân, ảnh đại diện, thông báo, sắp xếp

- **Controller:** `ProfileController` (đổi tên, đổi mật khẩu, đổi/xóa ảnh đại diện); `PublicPostController` (tham số `sort`).
- **Model:** `User` (`profile`, `updateName`, `updatePassword`, `updateAvatar`), `UserNotification`, `Post` (`publishedList` có `$sort`, `readingMinutes`).
- **View/điểm vào:** `trang-ca-nhan.php`, `app/Views/profile/show.php`, `assets/css/avatar.css`, `assets/css/profile.css`,
  helper `avatarUrl/avatarInner/avatarTag/notificationBanner` trong `bootstrap/app.php`.
- **Cần giải thích:** vì sao Admin bị chặn khỏi trang cá nhân, đổi mật khẩu phải xác nhận mật khẩu hiện tại, thông báo hiện một lần
  rồi đánh dấu `read_at`, "Đã thu hồi" được tính từ vai trò hiện tại thay vì lưu thêm trạng thái.

## Người phụ trách 8 tình huống kiểm thử

| # | Tình huống | Người |
|---|---|---|
| 1 | Khách không xem được bài nháp qua URL | Vũ Học |
| 2 | Tác giả A không sửa bài của tác giả B | Khánh Trung |
| 3 | Biên tập duyệt thì bài hiện ở trang chủ | Hải Vân |
| 4 | Slug trùng được xử lý | Khánh Trung |
| 5 | Upload file không phải ảnh bị từ chối | Khánh Trung |
| 6 | Bình luận chứa script không chạy | Vũ Học (hiển thị), Hà Anh (thao tác duyệt bằng Admin), Hải Vân (giải thích endpoint) |
| 7 | Tìm kiếm chỉ trả bài đã xuất bản | Vũ Học |
| 8 | CSRF sai thì thao tác bị từ chối | Hà Anh |

## Kịch bản demo (mỗi người thao tác phần của mình)

Khánh Trung tạo bài nháp và gửi duyệt → Hải Vân duyệt → Vũ Học tìm và đọc bài, gửi bình luận →
Hà Anh (Admin) duyệt bình luận.
