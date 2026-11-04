# 2. Kiến trúc và cấu trúc thư mục

## 2.1 Tổng quan

TaskFlow dùng PHP 8.1+ làm backend và phục vụ frontend cùng origin. PHP xử lý route, xác thực session, nghiệp vụ và truy vấn MySQL qua PDO. JavaScript gọi `/api/...` bằng `fetch()`.

```text
Trình duyệt
  ├─ GET /                    → php-backend/router.php trả app/templates/index.html
  ├─ GET /static/...          → router trả app/static/*
  └─ fetch /api/...           → PHP API → PDO prepared statements → MySQL
```

## 2.2 Cấu trúc liên quan

```text
TaskFlow/
├── app/
│   ├── templates/index.html  # Trang SPA
│   └── static/               # CSS, JavaScript, favicon
├── php-backend/
│   ├── router.php            # Front controller và static file routing
│   ├── public/index.php      # API, session, validation, PDO
│   └── migrations/           # SQL migration cho database đã tồn tại
├── sql/schema.sql            # Schema database mới
├── .env.example              # DATABASE_URL, COOKIE_SECURE
└── README.md                 # Cài đặt và chạy PHP backend
```

## 2.3 Trách nhiệm

- `php-backend/router.php`: phục vụ frontend/static an toàn và chuyển API tới front controller.
- `php-backend/public/index.php`: xử lý auth, account, CRUD task, tìm kiếm/lọc, dashboard; dùng PDO prepared statements.
- `sql/schema.sql`: tạo database và bảng cho cài đặt mới.
- `php-backend/migrations/`: thay đổi schema theo phiên bản; migration chỉ chạy một lần.
- `app/static/app.js`: gọi API cùng origin, không quyết định quyền sở hữu dữ liệu.

## 2.4 Session và bảo mật

- Session dùng cookie HttpOnly, SameSite=Lax; bật `COOKIE_SECURE=true` khi chạy sau HTTPS.
- Mật khẩu dùng `PASSWORD_ARGON2ID`; không lưu mật khẩu gốc.
- Mọi API task truy vấn theo `user_id` trong session.
- SQL động chỉ dùng placeholder/prepared statements; không nối dữ liệu đầu vào vào câu SQL.
- `php-backend/public/index.php` log lỗi nội bộ, API không trả stack trace.
