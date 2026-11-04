# 3. Thiết kế database MySQL

## 3.1 ERD

```text
users 1 ─────────── N tasks
users.id             tasks.user_id (FK, ON DELETE CASCADE)
```

## 3.2 Bảng `users`

| Cột             | Kiểu         | Ràng buộc                 | Ý nghĩa                              |
| --------------- | ------------ | ------------------------- | ------------------------------------ |
| `id`            | INT          | PK, AUTO_INCREMENT        | Mã người dùng.                       |
| `username`      | VARCHAR(40)  | NOT NULL, UNIQUE          | Tên đăng nhập.                       |
| `email`         | VARCHAR(254) | NOT NULL, UNIQUE          | Email.                               |
| `password_hash` | VARCHAR(255) | NOT NULL                  | Argon2 hash; không lưu mật khẩu gốc. |
| `created_at`    | DATETIME     | DEFAULT CURRENT_TIMESTAMP | Thời điểm tạo tài khoản.             |

## 3.3 Bảng `tasks`

| Cột           | Kiểu         | Ràng buộc                         | Ý nghĩa                  |
| ------------- | ------------ | --------------------------------- | ------------------------ |
| `id`          | INT          | PK, AUTO_INCREMENT                | Mã công việc.            |
| `user_id`     | INT          | FK → users.id, NOT NULL, INDEX    | Chủ sở hữu.              |
| `title`       | VARCHAR(160) | NOT NULL                          | Tiêu đề.                 |
| `description` | TEXT         | NOT NULL, default rỗng ở ứng dụng | Mô tả.                   |
| `status`      | VARCHAR(20)  | NOT NULL, mặc định `todo`         | `todo`, `doing`, `done`. |
| `priority`    | VARCHAR(20)  | NOT NULL, mặc định `medium`       | `low`, `medium`, `high`. |
| `due_date`    | DATE         | NULL                              | Hạn hoàn thành.          |
| `created_at`  | DATETIME     | DEFAULT CURRENT_TIMESTAMP         | Thời điểm tạo.           |
| `updated_at`  | DATETIME     | tự cập nhật                       | Thời điểm sửa gần nhất.  |

Index MVP: `tasks.user_id`, `tasks.status`. Unique index cho `users.username` và `users.email`.

## 3.4 Tạo schema

SQL đầy đủ nằm trong `sql/schema.sql`. File tạo database `taskflow` với `utf8mb4`, user ứng dụng `taskflow`@`localhost`, bảng, foreign key, index và CHECK constraint cho status/priority.

Giá trị demo trong file là `taskflow_dev_password`; chỉ dùng local. Nếu đã có tài khoản ứng dụng riêng, thay user/password trong SQL và đồng bộ URL trong `.env`.

## 3.5 Quy tắc truy vấn an toàn

Mọi route task phải áp dụng phạm vi chủ sở hữu, ví dụ về ý tưởng:

```text
SELECT ... FROM tasks
WHERE tasks.id = :task_id AND tasks.user_id = :current_user_id
```

Không chỉ kiểm tra `task_id`; nếu không ràng buộc `user_id`, người đăng nhập có thể đoán ID và xem/sửa task của tài khoản khác.

## 3.6 Khởi tạo bảng

Khởi tạo database mới bằng cách chạy `sql/schema.sql`. Với database đã tồn tại, áp dụng các file SQL migration trong `php-backend/migrations/` đúng thứ tự và chỉ chạy mỗi migration một lần. PHP backend không tự tạo hoặc tự sửa schema khi khởi động.
