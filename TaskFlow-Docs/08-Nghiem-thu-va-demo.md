# 8. Nghiệm thu và kịch bản demo

## 8.1 Checklist chức năng

### Auth

- [ ] Đăng ký tài khoản mới thành công.
- [ ] Password hash trong DB khác mật khẩu gốc, dạng Argon2.
- [ ] Trùng username/email trả lỗi rõ ràng.
- [ ] Đăng nhập đúng thành công; sai mật khẩu trả 401.
- [ ] Reload trang vẫn còn đăng nhập.
- [ ] Logout rồi gọi `/api/dashboard` nhận 401.

### Tasks

- [ ] Tạo task với chỉ title.
- [ ] Tạo task gồm description, status, priority, due date.
- [ ] Không tạo được title chỉ có khoảng trắng.
- [ ] Sửa đầy đủ field và thấy dữ liệu mới.
- [ ] Xóa task sau khi xác nhận.
- [ ] User thứ hai không xem/sửa/xóa task của user thứ nhất.

### Search/dashboard

- [ ] Search khớp title.
- [ ] Search khớp description.
- [ ] Lọc riêng Todo/Doing/Done.
- [ ] Lọc riêng Low/Medium/High.
- [ ] Thống kê dashboard khớp danh sách task.
- [ ] Task quá hạn chưa hoàn thành được đếm; task Done không tính quá hạn.

### UI/robustness

- [ ] Có loading/empty/error state.
- [ ] Dữ liệu có `<script>` hiển thị như text, không chạy script.
- [ ] Layout xem được trên điện thoại.
- [ ] API docs và health route mở được.

## 8.2 Kịch bản demo 3–5 phút

1. Mở trang TaskFlow, tạo tài khoản demo.
2. Tạo “Ôn tập lập trình” — Doing, High, hạn ngày mai.
3. Tạo “Hoàn thiện báo cáo” — Todo, Medium, hạn hôm qua.
4. Tạo “Thiết kế database” — Done, Low.
5. Trình bày dashboard đếm 3 task và 1 task quá hạn.
6. Search `báo cáo`, sau đó lọc `Todo`.
7. Sửa “Ôn tập lập trình” thành Done và cho thấy dashboard cập nhật.
8. Logout, thử truy cập API riêng tư để minh họa phiên đăng nhập.

## 8.3 Điểm cần trình bày

- Vì sao dùng session cookie: trình duyệt tự gửi cookie cùng origin, dễ dùng cho frontend đơn giản.
- Vì sao Argon2: database không giữ password dạng rõ.
- Vì sao mọi task query lọc theo `user_id`: cô lập dữ liệu giữa các tài khoản.
- Vì sao PHP phục vụ cùng origin: đơn giản hóa triển khai UI, static files và API trong MVP.
- Vì sao Bootstrap CDN: dựng responsive UI nhanh, phù hợp phạm vi MVP.
