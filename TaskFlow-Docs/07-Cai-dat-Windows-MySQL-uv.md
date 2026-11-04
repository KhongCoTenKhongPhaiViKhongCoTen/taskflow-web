# 7. Cài đặt Windows, MySQL và PHP

## 7.1 Yêu cầu

- Windows 10/11.
- PHP 8.1+ có `pdo_mysql`, `mbstring` và Argon2id.
- MySQL 8.x (Laragon có thể quản lý MySQL và PHP).
- Không cần Python, uv, Composer hoặc Apache để chạy local bằng PHP built-in server.

Kiểm tra PHP và extension:

```powershell
php -v
php -m
php -r "var_dump(in_array('argon2id', password_algos(), true));"
```

## 7.2 Tạo database

Khởi động MySQL trong Laragon. Chạy `sql/schema.sql` bằng MySQL CLI (điều chỉnh đường dẫn theo phiên bản cài đặt):

```powershell
Get-Content -Raw .\sql\schema.sql | & "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -p
```

Nếu root không có mật khẩu, bỏ `-p`. Schema tạo database `taskflow`, user demo và bảng users/tasks. Mật khẩu demo chỉ dành cho local.

Nếu database đã được tạo trước khi có các tính năng “Ngày của tôi” và “Quan trọng”, áp dụng migration này một lần:

```powershell
Get-Content -Raw .\php-backend\migrations\001_my_day_and_important.sql | & "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -p
```

## 7.3 Cấu hình

Tại thư mục gốc dự án:

```powershell
Copy-Item .env.example .env
```

Chỉnh `DATABASE_URL` trong `.env` cho khớp user, password, host, port và database. Đặt `COOKIE_SECURE=false` cho local HTTP; bật `true` khi deploy sau HTTPS.

## 7.4 Chạy ứng dụng

Từ thư mục gốc:

```powershell
php -S 127.0.0.1:8080 php-backend/router.php
```

Mở app tại `http://127.0.0.1:8080/`; health check tại `/health`. Dừng bằng `Ctrl+C`.

PHP built-in server chỉ dùng phát triển local, không dùng làm production server. Production cần PHP-FPM/Apache/Nginx sau HTTPS.
