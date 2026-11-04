# Bộ tài liệu TaskFlow

Bộ tài liệu mô tả MVP TaskFlow quản lý công việc cá nhân. **Backend hiện dùng PHP 8.1+, PDO và MySQL**; frontend là HTML/CSS/JavaScript thuần do PHP phục vụ cùng origin.

## Tài liệu

1. [Phạm vi và yêu cầu](01-Pham-vi-va-yeu-cau.md)
2. [Kiến trúc và cấu trúc PHP](02-Kien-truc-va-cau-truc.md)
3. [Thiết kế database](03-Thiet-ke-database.md)
4. [Đặc tả API](04-Dac-ta-API.md)
5. [Thiết kế frontend](05-Thiet-ke-frontend.md)
6. [Lộ trình backend PHP](06-Lo-trinh-implement.md)
7. [Cài đặt Windows, MySQL và PHP](07-Cai-dat-Windows-MySQL-uv.md)
8. [Nghiệm thu và demo](08-Nghiem-thu-va-demo.md)
9. [Xử lý lỗi PHP](09-Xu-ly-loi.md)

## Nguyên tắc

- User chỉ xem và thao tác trên task thuộc tài khoản của mình.
- PHP phục vụ UI và API cùng origin.
- Mật khẩu hash bằng Argon2id; session dùng cookie HttpOnly, SameSite=Lax.
- Truy vấn dữ liệu dùng PDO prepared statements.
- MySQL là database chính; cập nhật schema qua SQL migration.
