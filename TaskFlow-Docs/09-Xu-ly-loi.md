# 9. Xử lý lỗi thường gặp (PHP backend)

## `could not find driver`

PHP chưa bật extension `pdo_mysql`. Kiểm tra `php -m`, bật extension trong `php.ini`, rồi mở terminal mới.

## `Call to undefined function mb_strlen()`

Bật extension `mbstring` trong `php.ini` rồi khởi động lại PHP server.

## PHP không hỗ trợ Argon2id

Kiểm tra bằng `php -r "var_dump(in_array('argon2id', password_algos(), true));"`. Dùng bản PHP build có Argon2id; không hạ xuống thuật toán yếu hơn.

## MySQL báo connection refused

- Bật MySQL trong Laragon và xác nhận cổng `3306`.
- Kiểm tra `DATABASE_URL` trong `.env`.
- Chạy `sql/schema.sql` trước lần chạy đầu.

## `Access denied for user`

Khớp user/password trong `DATABASE_URL` với user được tạo từ schema. Nếu mật khẩu chứa ký tự đặc biệt, URL-encode user/password.

## `Unknown column my_day_date` hoặc `is_important`

Database cũ chưa được migrate. Chạy `php-backend/migrations/001_my_day_and_important.sql` một lần.

## API trả `401`

Đăng nhập lại và đảm bảo browser gửi cookie cùng origin. PHP dùng cookie `taskflow_php_session`; kiểm tra `COOKIE_SECURE` không bật trên HTTP local.

## CSS/JavaScript không cập nhật

Tải lại bằng `Ctrl+F5`; template có cache-busting cho CSS/JS. Nếu đang deploy qua proxy, kiểm tra cache static assets.

## Trang không mở

Khởi động server tại thư mục gốc dự án bằng `php -S 127.0.0.1:8080 php-backend/router.php`. PHP built-in server chỉ dành cho phát triển local.
