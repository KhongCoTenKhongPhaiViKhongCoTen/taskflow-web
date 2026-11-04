# 1. Phạm vi và yêu cầu

## 1.1 Mục tiêu

TaskFlow là website quản lý công việc cá nhân. Người dùng tạo tài khoản, đăng nhập, tạo và theo dõi công việc theo trạng thái, mức ưu tiên và hạn hoàn thành.

## 1.2 Đối tượng và vai trò

MVP chỉ có một vai trò: **người dùng đã đăng nhập**. Mỗi tài khoản quản lý dữ liệu riêng. Không có tài khoản quản trị hoặc chia sẻ công việc giữa người dùng.

## 1.3 Chức năng

| Mã   | Chức năng           | Yêu cầu chấp nhận                                                                                |
| ---- | ------------------- | ------------------------------------------------------------------------------------------------ |
| F-01 | Đăng ký             | Tên đăng nhập 3–40 ký tự, email hợp lệ, mật khẩu ít nhất 8 ký tự; email và username không trùng. |
| F-02 | Đăng nhập/đăng xuất | Đăng nhập bằng username và mật khẩu; đăng xuất xóa phiên.                                        |
| F-03 | Quản lý phiên       | Trang tải lại vẫn giữ đăng nhập; người chưa đăng nhập không gọi được API riêng tư.               |
| F-04 | Tạo công việc       | Lưu tiêu đề bắt buộc, mô tả, trạng thái, ưu tiên và hạn tùy chọn.                                |
| F-05 | Sửa công việc       | Người dùng sửa được công việc thuộc tài khoản mình.                                              |
| F-06 | Xóa công việc       | Người dùng xóa được công việc thuộc tài khoản mình; giao diện hỏi xác nhận.                      |
| F-07 | Trạng thái          | Chỉ nhận `todo`, `doing`, `done`.                                                                |
| F-08 | Ưu tiên             | Chỉ nhận `low`, `medium`, `high`.                                                                |
| F-09 | Tìm kiếm            | Tìm chuỗi trong tiêu đề và mô tả.                                                                |
| F-10 | Lọc                 | Lọc theo trạng thái và mức ưu tiên; hỗ trợ lựa chọn tất cả.                                      |
| F-11 | Dashboard           | Hiển thị tổng số, số cần làm, đang làm, hoàn thành và quá hạn.                                   |

## 1.4 Quy tắc nghiệp vụ

- Tiêu đề được trim khoảng trắng đầu/cuối và không được rỗng.
- Tài khoản chỉ đọc, sửa, xóa công việc có `user_id` bằng ID trong phiên hiện tại.
- Công việc quá hạn khi `due_date` trước ngày hiện tại và `status != done`.
- Xóa tài khoản (nếu bổ sung sau) sẽ xóa công việc theo khóa ngoại `ON DELETE CASCADE`.
- Thời gian lưu trong MySQL dùng giờ máy chủ; hạn công việc là kiểu `DATE`, không có giờ/phút.

## 1.5 Yêu cầu chất lượng MVP

- Backend trả lỗi HTTP phù hợp và không lộ hash mật khẩu.
- Mật khẩu được băm bằng Argon2.
- Không ghép giá trị người dùng trực tiếp vào SQL; dùng PDO prepared statements.
- Nội dung người dùng nhập phải được escape khi render bằng JavaScript.
- Giao diện co giãn cho màn hình điện thoại và máy tính.
- Có hướng dẫn cài đặt, cấu hình database và chạy local trên Windows PowerShell.

## 1.6 Ngoài phạm vi

Thông báo email, quên mật khẩu, xác minh email, chia sẻ task, team, danh mục tùy chỉnh, upload file, lịch kéo thả, phân quyền admin, OAuth, API public, triển khai cloud và đa ngôn ngữ đầy đủ không thuộc MVP.
