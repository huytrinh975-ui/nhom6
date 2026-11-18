# Thư viện tạo QR bằng PHP

Tác giả: Kazuhiko Arase. Giấy phép: MIT (xem LICENSE).

Nguồn: https://github.com/kazuhikoarase/qrcode-generator

Commit cố định: `95af9c2e1249337047ca478ccf98650a0a30af13`

Chỉ đóng gói file `php/qrcode.php`; không đóng gói bản JavaScript, Java hoặc mẫu chạy. File nguồn giữ nguyên.

SHA-256: `839337a00e1ab8be1916ab20702d724aa41cdd7991dd7fc8187ffbc602998b4e`

Ứng dụng gọi `QRCode::getMinimumQRCode` để lấy ma trận, rồi tự vẽ SVG với khoảng trắng bốn ô. Không cần Composer, GD hoặc kết nối mạng khi tạo mã.
