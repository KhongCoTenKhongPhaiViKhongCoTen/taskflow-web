# TaskFlow

TaskFlow MVP dùng **PHP 8.1+**, PDO/MySQL và JavaScript thuần. PHP phục vụ frontend cùng origin và toàn bộ API; không cần Python, Composer hay frontend build step.

## Yêu cầu

- PHP 8.1 trở lên với extension `pdo_mysql`, `mbstring` và hỗ trợ `PASSWORD_ARGON2ID`.
- MySQL 8.x.

## Cài đặt và chạy trên Windows

1. Khởi động MySQL qua Laragon hoặc dịch vụ MySQL khác.
2. Tạo database và user ứng dụng bằng `sql/schema.sql`. Giá trị mật khẩu trong schema chỉ dành cho local demo.
3. Tạo `.env` từ `.env.example` rồi chỉnh `DATABASE_URL` theo thông tin MySQL. Ví dụ:

```dotenv
DATABASE_URL=mysql+pymysql://taskflow:taskflow_dev_password@127.0.0.1:3306/taskflow?charset=utf8mb4
COOKIE_SECURE=false
```

4. Nếu database đã tồn tại từ trước, chạy migration một lần:

```powershell
Get-Content -Raw .\php-backend\migrations\001_my_day_and_important.sql | mysql -u root -p
```

Migration thêm các cột “Ngày của tôi” và “Quan trọng”. Bỏ `-p` nếu tài khoản MySQL không có mật khẩu. Không chạy lại migration sau khi đã áp dụng.

5. Tại thư mục gốc dự án, chạy PHP built-in server:

```powershell
php -S 127.0.0.1:8080 php-backend/router.php
```

Mở `http://127.0.0.1:8080/`. Health check: `http://127.0.0.1:8080/health`.

Để dùng HTTPS production, đặt `COOKIE_SECURE=true` và chạy sau web server có TLS. Không dùng PHP built-in server làm production server.

## API

- Auth: `POST /api/register`, `POST /api/login`, `POST /api/logout`, `GET /api/me`.
- Account: `PUT /api/account` cập nhật username/email hoặc đổi mật khẩu; cần gửi mật khẩu hiện tại.
- Tasks: `GET|POST /api/tasks`, `PUT|DELETE /api/tasks/{id}`, `GET /api/dashboard`.
- Task filters: `q`, `status`, `priority`, `view=all|my_day|important`.
- Task fields: `title`, `description`, `status`, `priority`, `due_date`, `my_day_date`, `is_important`.

PHP dùng prepared statements, Argon2id cho mật khẩu và session cookie HttpOnly/SameSite=Lax. Tất cả thao tác task giới hạn theo user trong session. Swagger không được cấu hình; API contract tham khảo `TaskFlow-Docs/04-Dac-ta-API.md`.
