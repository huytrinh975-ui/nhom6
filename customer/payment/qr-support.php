<?php
declare(strict_types=1);

/** Mã QR mô phỏng: không chứa tài khoản ngân hàng, thông tin cá nhân hay URL localhost. */
function cbp_qr_fingerprint(array $order, array $seats): string
{
    $values = [
        (int) $order['id'], (int) $order['showtime_id'],
        $order['booking_code'], $order['total_amount'], $order['start_time'], $order['end_time'],
        array_map(static function (array $seat): array {
            return [(int) $seat['seat_id'], $seat['seat_number'], $seat['price']];
        }, $seats),
    ];
    return hash('sha256', json_encode($values, JSON_THROW_ON_ERROR));
}

/** Lưu báo giá trong session, không tạo giao dịch khi chỉ mở/đọc mã QR. */
function cbp_qr_quote(array $order, array $seats, int $customerId): array
{
    $bookingId = (int) $order['id'];
    $requestKey = cbp_payment_key($bookingId);
    $fingerprint = cbp_qr_fingerprint($order, $seats);
    $quote = $_SESSION['cbp_qr_quotes'][$bookingId] ?? null;
    if (is_array($quote) && ($quote['customer_id'] ?? null) === $customerId
        && ($quote['request_key'] ?? '') === $requestKey && ($quote['fingerprint'] ?? '') === $fingerprint
        && ($quote['expires_at'] ?? 0) > time()) {
        return $quote;
    }
    $start = cbp_datetime($order['start_time']);
    if (!$start) { throw new CBPUserError('Không xác định được giờ chiếu.', 409); }
    $token = bin2hex(random_bytes(32));
    $quote = [
        'customer_id' => $customerId,
        'request_key' => $requestKey,
        'token' => $token,
        'fingerprint' => $fingerprint,
        'reference' => 'CB' . strtoupper(substr($token, 0, 16)),
        'expires_at' => min(time() + 600, $start->getTimestamp()),
    ];
    $_SESSION['cbp_qr_quotes'][$bookingId] = $quote;
    return $quote;
}

/** Kiểm tra lại bên trong transaction ngay trước khi ghi nhận kết quả. */
function cbp_qr_require_quote(array $order, array $seats, int $customerId, string $requestKey, string $token): array
{
    $quote = $_SESSION['cbp_qr_quotes'][(int) $order['id']] ?? null;
    if (!is_array($quote) || ($quote['customer_id'] ?? null) !== $customerId
        || !preg_match('/\A[a-f0-9]{64}\z/', $token)
        || !hash_equals($quote['token'] ?? '', $token)
        || !hash_equals($quote['request_key'] ?? '', $requestKey)) {
        throw new CBPUserError('Mã QR không còn thuộc phiên thao tác này. Hãy mở lại trang chuyển khoản.', 403);
    }
    if (($quote['expires_at'] ?? 0) <= time()) {
        throw new CBPUserError('Mã QR đã hết hạn. Hãy mở lại đơn hàng để lấy mã mới.', 409);
    }
    if (!hash_equals($quote['fingerprint'] ?? '', cbp_qr_fingerprint($order, $seats))) {
        throw new CBPUserError('Thông tin đơn hàng vừa thay đổi. Hãy kiểm tra đơn và lấy mã QR mới.', 409);
    }
    return $quote;
}

function cbp_qr_payload(array $order, array $quote): string
{
    $amount = cbp_cents($order['total_amount']);
    if ($amount === null) { throw new CBPUserError('Số tiền đơn hàng không hợp lệ.', 409); }
    return "CINEBOOK - THU NGHIEM\nKHONG CHUYEN TIEN THAT\n"
        . 'DON: ' . $order['booking_code'] . "\n"
        . 'SO TIEN: ' . cbp_decimal($amount) . " VND\n"
        . 'NOI DUNG: ' . $quote['reference'] . "\n"
        . 'HET HAN: ' . date('Y-m-d H:i:s', $quote['expires_at']) . ' +07:00';
}

/** Vẽ SVG tại server bằng thư viện PHP đóng gói sẵn; không gọi dịch vụ bên ngoài. */
function cbp_qr_svg(string $payload): string
{
    if (strlen($payload) > 512) { throw new RuntimeException('QR payload exceeds module limit'); }
    require_once __DIR__ . '/vendor/qrcode-generator/qrcode.php';
    $qr = QRCode::getMinimumQRCode($payload, QR_ERROR_CORRECT_LEVEL_M);
    $size = $qr->getModuleCount();
    $edge = $size + 8; // Khoảng trắng bắt buộc: 4 ô ở mỗi cạnh.
    $path = '';
    for ($row = 0; $row < $size; $row++) {
        for ($col = 0; $col < $size; $col++) {
            if ($qr->isDark($row, $col)) {
                $path .= 'M' . ($col + 4) . ',' . ($row + 4) . 'h1v1h-1z';
            }
        }
    }
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $edge . ' ' . $edge . '" width="340" height="340" role="img" aria-label="Mã QR chuyển khoản thử nghiệm" shape-rendering="crispEdges">'
        . '<rect width="100%" height="100%" fill="#fff"/><path d="' . $path . '" fill="#000"/></svg>';
}
