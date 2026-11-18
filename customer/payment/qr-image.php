<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
try {
    $customer = cbp_require_customer();
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        throw new CBPUserError('Ảnh QR chỉ hỗ trợ yêu cầu xem.', 405);
    }
    $bookingId = cbp_request_id('booking_id', $_GET);
    $order = cbp_owned_order($bookingId, (int) $customer['id']);
    $seats = cbp_seats($bookingId);
    $issues = cbp_payment_issues($order, $seats);
    if ($issues) { throw new CBPUserError($issues[0], 409); }
    $quote = $_SESSION['cbp_qr_quotes'][$bookingId] ?? null;
    $reference = cbp_text($_GET['ref'] ?? null, 20);
    if (!is_array($quote) || !hash_equals($quote['reference'] ?? '', $reference)) {
        throw new CBPUserError('Hãy mở trang chuyển khoản để lấy mã QR hiện tại.', 403);
    }
    cbp_qr_require_quote($order, $seats, (int) $customer['id'], $quote['request_key'], $quote['token']);
    $svg = cbp_qr_svg(cbp_qr_payload($order, $quote));
    header('Content-Type: image/svg+xml; charset=UTF-8');
    header('Content-Disposition: inline; filename="cinebook-qr.svg"');
    header("Content-Security-Policy: default-src 'none'; style-src 'none'; sandbox");
    echo $svg;
} catch (Throwable $error) { cbp_handle_error($error); }
