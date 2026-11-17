# Hướng dẫn sử dụng và bàn giao

## 1. Chuẩn bị

Làm các bước ở README.md tại thư mục gốc: cấu hình MySQL cá nhân, chuẩn bị 8 bảng nền, mở trang chủ và bấm Thanh toán vé. Trang setup bổ sung 4 bảng riêng nếu thiếu và tạo đơn thử có suất chiếu tương lai.

Tài khoản mẫu mã 2 trong database_demo.sql chỉ phục vụ kiểm thử. Những trang dùng tài khoản mẫu được giới hạn ở localhost và database cinema_booking_test. Khi ghép đăng nhập thật, dùng phiên customer_user sau bước xác thực mật khẩu.

## 2. Các tình huống cần thử

1. **Đơn trước thanh toán:** xem đủ phim, rạp, phòng, ngày/giờ, ghế, số tiền và khách hàng.
2. **Thanh toán thất bại:** chọn kết quả thất bại; đơn không phát hành vé. Mở lại trang thanh toán để nhận biểu mẫu mới rồi thử thành công.
3. **Thanh toán thành công:** website ghi nhận giao dịch, xác nhận đơn và tạo đúng một vé điện tử.
4. **Gửi lại yêu cầu:** tải lại kết quả hoặc gửi lại cùng biểu mẫu không tạo vé/giao dịch trùng.
5. **Xem vé:** mở Vé của tôi, xem chi tiết, tải HTML. Bấm Mở bản in vé, nhấn Ctrl + P hoặc Command + P để in/lưu PDF.
6. **Lịch sử:** tìm mã đơn, lọc trạng thái và xem các lần thanh toán.
7. **Hủy hợp lệ:** đơn còn đủ thời gian được hủy, vé chuyển sang trạng thái đã hủy và ghi nhận hoàn tiền mô phỏng.
8. **Hủy sát giờ:** tạo đơn với suất sau khoảng 30 phút, thanh toán rồi thử hủy. Website từ chối khi không còn đủ 60 phút.
9. **Chưa thanh toán:** hủy đơn chưa thanh toán không ghi nhận hoàn tiền.
10. **Quyền sở hữu:** dùng tài khoản khác mở mã đơn hoặc mã vé không thuộc mình sẽ bị từ chối.
11. **Suất đã bắt đầu:** không nhận thanh toán mới.
12. **Phiên không hợp lệ:** đăng xuất, khóa tài khoản hoặc dùng tài khoản admin không mở được dữ liệu khách hàng.

## 3. Bàn giao cho thành viên còn lại

- Giữ thư mục customer/payment cùng cấp config.
- Database phải dùng đúng cấu trúc đã thống nhất trong database_demo.sql.
- Phần đăng nhập mở session và chỉ ghi customer_user sau khi kiểm tra mật khẩu thành công.
- Phần đặt vé tạo bookings và booking_details rồi chuyển đến checkout.php với booking_id.
- Gắn menu theo integration/menu-root.html hoặc integration/menu-admin.html.
- Khi nhóm đã có index.php riêng, ghép menu vào trang chủ đó; index.php trong gói là trang vào mẫu.

## 4. Xử lý lỗi thường gặp

- **Not Found:** kiểm tra tên thư mục trong htdocs và vị trí customer/payment/index.php. Không lồng customer/payment hai lần.
- **Chưa tìm thấy cấu hình:** chép config/database.example.php thành config/database.php.
- **Không kết nối database:** đối chiếu host, port, tên database và mật khẩu MySQL trên máy đang chạy.
- **Unknown column:** schema đang khác bản module yêu cầu. So sánh tên cột với database_demo.sql trước khi sửa; không đổi riêng total_price thành total_amount.
- **Vui lòng đăng nhập:** cần phiên khách hàng hợp lệ; tài khoản mẫu chỉ được chấp nhận trong chế độ kiểm thử đã nêu.
- **Cần cài đặt phần thanh toán:** tạo 4 bảng cbp_* bằng bước cài đặt hoặc file SQL bổ sung.
- **Đơn cũ hết giờ chiếu:** dùng Tạo đơn thử nghiệm để tạo một đơn có suất tương lai.
- **In vé:** trang in không tự mở hộp thoại in. Dùng phím tắt hoặc menu In của trình duyệt.

## 5. Phạm vi

Gói hiện có 7 chức năng thanh toán/vé được giao. Giao dịch và hoàn tiền là mô phỏng. Phần đăng nhập, chọn phim/suất/ghế và quản trị do nhóm ghép theo giao diện dữ liệu đã thống nhất.

