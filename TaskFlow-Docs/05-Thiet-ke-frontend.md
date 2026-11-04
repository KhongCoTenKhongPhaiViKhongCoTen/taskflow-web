# 5. Thiết kế frontend

## 5.1 Công nghệ

- HTML5 semantic cơ bản.
- Bootstrap 5 qua CDN để dựng grid, form, buttons, badge, modal, nav và toast.
- CSS riêng cho màu sắc, khoảng cách, responsive và trạng thái task.
- JavaScript thuần dùng `fetch()`; không thêm React/Vue.
- PHP phục vụ trang HTML và static files cùng origin; JavaScript gọi API PHP qua `fetch()`.

## 5.2 Màn hình

### Đăng nhập/đăng ký

- Một panel, chuyển tab login/register.
- Username và password; email chỉ hiện ở đăng ký.
- Thông báo lỗi API đặt cạnh form.
- Sau thành công gọi `GET /api/me` hoặc dùng response auth để hiện dashboard.

### Dashboard

- Thẻ thống kê tổng, cần làm, đang làm, hoàn thành.
- Dòng thông tin số task quá hạn.
- Nút “Tạo công việc”.

### Danh sách task

- Ô tìm kiếm; debounce khoảng 200–300 ms.
- Dropdown trạng thái và ưu tiên.
- Mỗi hàng hiển thị title, mô tả, status, priority, due date, nút sửa/xóa.
- Empty state khi danh sách rỗng hoặc filter không có kết quả.

### Modal tạo/sửa

- Title bắt buộc; description tùy chọn.
- Status, priority, date.
- Nút lưu/hủy và vùng lỗi validation.
- Cùng một modal cho create/update; ID ẩn phân biệt chế độ.

## 5.3 Quy ước JavaScript

- Gom gọi HTTP vào hàm `api(path, options)` để xử lý JSON, cookie và lỗi.
- Sau mỗi create/update/delete, tải lại dashboard và danh sách.
- Hiển thị toast sau thao tác thành công.
- Hỏi xác nhận trước khi xóa.
- Khi API trả `401`, chuyển về màn hình đăng nhập hoặc tải lại trạng thái session.
- Disable nút lưu trong lúc request để tránh submit lặp.

## 5.4 An toàn khi render dữ liệu

Dữ liệu title/description do người dùng nhập. Nếu render bằng `innerHTML`, phải escape tối thiểu `& < > " '` trước. Phương án tốt hơn cho nội dung text là tạo DOM element và gán `textContent`.

Không chèn description trực tiếp vào HTML template string chưa escape. Không lưu password vào `localStorage`/`sessionStorage`; dùng cookie phiên HttpOnly.

## 5.5 Trạng thái UI cần có

- Đang tải dữ liệu.
- Không có task.
- Lỗi mạng/server.
- Đăng nhập hết hạn.
- Form đang submit.
- Xóa cần xác nhận.
- Task quá hạn (trừ task đã hoàn thành).
