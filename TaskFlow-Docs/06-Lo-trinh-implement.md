# 6. Lộ trình triển khai backend PHP

Backend hiện tại dùng PHP và PDO/MySQL. Các bước dưới đây giúp cài đặt/kiểm tra bản PHP.

## Bước 1 — Chuẩn bị môi trường

1. Cài PHP 8.1+ với extension `pdo_mysql`, `mbstring` và `PASSWORD_ARGON2ID`.
2. Khởi động MySQL 8.x.
3. Chạy `sql/schema.sql` cho database mới.
4. Tạo `.env` từ `.env.example` và cập nhật thông tin kết nối.
5. Chạy `php -S 127.0.0.1:8080 php-backend/router.php`.

**Hoàn tất khi:** trang chủ mở và `/health` trả `{"status":"ok"}`.

## Bước 2 — Database

- Schema mới: `sql/schema.sql`.
- Database cũ: chạy migration còn thiếu trong `php-backend/migrations/` đúng một lần.
- Ứng dụng dùng PDO prepared statements, không dùng ORM tự tạo/sửa schema.

## Bước 3 — Auth và account

- `POST /api/register`, `POST /api/login`, `POST /api/logout`, `GET /api/me`.
- Session cookie HttpOnly, SameSite=Lax; password hash Argon2id.
- `PUT /api/account` cập nhật username/email hoặc password; yêu cầu password hiện tại.

## Bước 4 — Task API

- `GET|POST /api/tasks`, `PUT|DELETE /api/tasks/{id}`.
- Tìm kiếm theo `q`, lọc status/priority và `view=all|my_day|important`.
- Mọi truy vấn đều áp dụng ownership theo session user.
- `GET /api/dashboard` thống kê trạng thái và quá hạn.

## Bước 5 — Frontend và nghiệm thu

Frontend hiện được PHP router phục vụ cùng origin. Kiểm tra đăng nhập, thao tác task, lọc Ngày của tôi/Quan trọng và quản lý tài khoản; xem checklist trong `08-Nghiem-thu-va-demo.md`.
