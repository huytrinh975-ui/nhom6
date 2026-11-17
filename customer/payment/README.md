# Thanh toán và vé điện tử

Đây là phần công việc độc lập dùng PHP/MySQLi, HTML và CSS, chuẩn bị để ghép vào repo mới.

Đọc **../../README.md** để chạy thử và thống nhất cấu trúc database/phiên đăng nhập với thành viên còn lại.

## Các trang chính

- index.php: điểm vào từ menu website, dẫn đến đơn hàng hoặc bước xác nhận tài khoản mẫu.
- checkout.php: kiểm tra phim, rạp, suất chiếu, ghế, khách hàng và số tiền.
- pay.php, result.php: thanh toán mô phỏng và kết quả.
- ticket.php, print-ticket.php, download-ticket.php: xem, mở bản in và tải vé HTML.
- tickets.php: các vé của khách hàng.
- history.php: lịch sử đơn hàng, tìm kiếm và bộ lọc.
- cancel.php, policy.php, settings.php: hủy đơn/vé và quy định.
- setup-demo.php, dev-login.php, demo.php: công cụ chạy thử trên localhost với database cinema_booking_test.
- bootstrap.php, domain.php, ui.php: phần dùng chung.

Mở bản in vé rồi nhấn **Ctrl + P / Command + P**, chọn máy in hoặc Save as PDF. Không có file mã chạy ở trình duyệt trong thư mục này.

