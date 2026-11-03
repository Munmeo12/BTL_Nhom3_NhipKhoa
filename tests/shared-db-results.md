# Kiểm thử tích hợp trên database dùng chung — ghi nhận ngày 2026-09-08

> Đây là kết quả một lần chạy cũ trên phiên bản code tại thời điểm đó. Hãy chạy lại trước khi nộp bài.
> Script nay có tên `shared-db-browser.cjs`; tiền tố bản ghi thử đổi từ `qa-ly-` thành `qa-shared-`.

Application revision tested: `79ff319`, incorporating commit `b9de069` (category, comment and Impact Box work).

## Result

- 198 browser/API/database assertions passed against the shared VPS with explicit user authorization.
- Used uniquely tagged fixture `qa-ly-0f688bde29cd228e`; no existing accounts or articles were used for mutations.
- Tested PHP development servers at project root and under `/btl_laptrinhweb/` (subdirectory URL compatibility, not an actual Apache process).
- Screenshots inspected at 375px and 1440px; automated layout checks also covered 768px.
- Cleanup completed. Fingerprints of all pre-existing rows in roles, users, categories, posts, comments and impact_box_items were unchanged.
- Auto-increment counters may advance as test records are inserted and deleted; they are deliberately not reset.

## Coverage

Admin routes, menu/CSS URLs, login/logout, reader access denial, category create/edit/delete, duplicate name/slug errors, form preservation, invalid category status, CSRF rejection, blocking category deletion when posts exist, comment approve/hide/show/delete, save dialog, duplicate saves, note edit/clear, ownership isolation, private/hidden article visibility, safe text rendering and preservation of the original post when unsaving.

## Running again

`tests/shared-db-browser.cjs` is deliberately opt-in via `--allow-shared-vps`. It creates temporary users (including an Admin), categories, posts and a comment visible on the shared site while running. It uses the project's configured database credentials; do not run without authorization for shared database writes. Set `PLAYWRIGHT_MODULE` and optionally `PHP_BINARY`/`CHROME_BINARY` to local runtime paths, then execute:

```text
node tests/shared-db-browser.cjs --allow-shared-vps
```

The fixture helper refuses a database other than `nhip_khoa` on `vultr-01`. A per-run ID journal is retained in the OS temporary directory (no passwords). Cleanup runs in `finally`; if the process is forcibly terminated, use the run ID from the output with:

```text
php tests/shared-db-fixture.php --allow-shared-vps cleanup qa-shared-<16-hex-run-id>
```

Cleanup only targets the tagged fixture. Unexpected interactions on test posts stop cleanup for manual review rather than cascading into another user's data.

## Not covered / unchanged scope

- The separate comment-submission branch was not merged then; comments were seeded as fixtures to test moderation.
- Homepage heart buttons and dynamic Impact Summary are still separate integration work.
- No schema/seed import, shared database reset, GitHub push or copy to the separate XAMPP checkout.
- The pre-existing uncommitted edit in `config/config.local.example.php` is excluded from commits and remains untouched.
