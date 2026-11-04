# 4. Đặc tả API

API dùng JSON, prefix `/api`. Request thay đổi dữ liệu gửi `Content-Type: application/json`; phiên được gửi tự động qua cookie cùng origin.

## 4.1 Đăng ký

`POST /api/register` — tạo user và đăng nhập luôn.

```json
{
  "username": "minhanh",
  "email": "minhanh@example.com",
  "password": "matkhau123"
}
```

Response `201`:

```json
{ "id": 1, "username": "minhanh" }
```

Lỗi: `409` trùng username/email; `422` dữ liệu không hợp lệ.

## 4.2 Đăng nhập và đăng xuất

`POST /api/login`:

```json
{ "username": "minhanh", "password": "matkhau123" }
```

Response `200`: `{ "id": 1, "username": "minhanh" }`; sai thông tin trả `401`.

`POST /api/logout`: xóa session, trả `{ "ok": true }`.

## 4.3 Tài khoản hiện tại

`GET /api/me` — cần session.

`PUT /api/account` — cập nhật username/email hoặc đổi mật khẩu; yêu cầu mật khẩu hiện tại. Request gồm các trường tài khoản cần cập nhật và `current_password`.

```json
{ "id": 1, "username": "minhanh" }
```

## 4.4 Liệt kê, tìm kiếm và lọc

`GET /api/tasks?q=bao%20cao&status=todo&priority=high`

Query parameters tùy chọn:

- `q`: tìm theo title/description.
- `status`: `todo|doing|done|all`.
- `priority`: `low|medium|high|all`.
- `view`: `all|my_day|important`; lọc theo Ngày của tôi hoặc Quan trọng.

Trả danh sách task của user hiện tại, sắp xếp hạn gần nhất trước; task không có hạn ở cuối.

## 4.5 Tạo task

`POST /api/tasks` — cần session.

```json
{
  "title": "Hoàn thiện báo cáo",
  "description": "Viết phần tổng kết",
  "status": "todo",
  "priority": "high",
  "due_date": "2026-11-10",
  "my_day_date": "2026-11-10",
  "is_important": true
}
```

`due_date` có thể `null`; `description` mặc định chuỗi rỗng; status mặc định `todo`; priority mặc định `medium`. Trả `201` cùng task tạo.

## 4.6 Sửa và xóa

- `PUT /api/tasks/{id}`: body theo cấu trúc tạo task; trả task cập nhật.
- `DELETE /api/tasks/{id}`: trả `204 No Content`.
- ID không tồn tại hoặc không thuộc user hiện tại: `404`.

## 4.7 Dashboard

`GET /api/dashboard` — cần session.

```json
{ "total": 8, "todo": 3, "doing": 2, "done": 3, "overdue": 1 }
```

## 4.8 Lỗi chuẩn

| HTTP | Dùng khi                                                          |
| ---- | ----------------------------------------------------------------- |
| 201  | Tạo mới thành công.                                               |
| 204  | Xóa thành công.                                                   |
| 401  | Chưa đăng nhập / thông tin đăng nhập sai.                         |
| 404  | Task không tồn tại hoặc không thuộc user.                         |
| 409  | Username/email đã được dùng.                                      |
| 422  | JSON hoặc trường không hợp lệ.                                    |
| 500  | Lỗi không dự kiến; log nội bộ, không trả stack trace cho browser. |
