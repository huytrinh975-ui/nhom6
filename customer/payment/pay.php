<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
try {
    $customer = cbp_require_customer();
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        cbp_require_post();
        $bookingId = cbp_request_id('booking_id', $_POST);
        $attemptId = cbp_pay($bookingId, (int) $customer['id'], cbp_text($_POST['request_key'] ?? null, 64), cbp_text($_POST['outcome'] ?? null, 10));
        cbp_redirect('result.php?attempt_id=' . $attemptId);
    }
    $bookingId = cbp_request_id('booking_id', $_GET);
    $order = cbp_owned_order($bookingId, (int) $customer['id']);
    $seats = cbp_seats($bookingId);
    $issues = cbp_payment_issues($order, $seats);
    if ($issues) { throw new CBPUserError($issues[0], 409); }
    $requestKey = cbp_payment_key($bookingId);
} catch (Throwable $error) { cbp_handle_error($error); }
cbp_header('Phương thức thanh toán', $customer, 'orders');
cbp_heading('Phương thức thanh toán', 'Chọn cách thanh toán cho đơn vé của bạn.', $order['booking_code']);
?>
<div class="checkout-grid">
    <section class="panel">
        <span class="pill info">THÔNG TIN ĐƠN HÀNG</span>
        <h2 class="space-top"><?= cbp_e($order['movie_title']) ?></h2>
        <p class="muted"><?= cbp_e($order['cinema_name']) ?> · <?= cbp_e($order['room_name']) ?></p>
        <p><?= cbp_e(cbp_date_label($order['start_time'])) ?></p>
        <div class="seat-chips"><?php foreach ($seats as $seat): ?><span class="seat-chip"><?= cbp_e($seat['seat_number']) ?></span><?php endforeach; ?></div>
        <div class="total-row"><span>Tổng tiền</span><strong><?= cbp_e(cbp_money(cbp_cents($order['total_amount']))) ?></strong></div>
        <a class="text-link" href="checkout.php?booking_id=<?= $bookingId ?>">Quay lại kiểm tra đơn hàng</a>
    </section>
    <section class="panel">
        <h2>Chọn phương thức</h2>
        <div class="qr-method-card space-top">
            <span class="pill info">MÃ QR</span>
            <h3 class="space-top">Chuyển khoản bằng mã QR</h3>
            <p class="muted">Quét mã để xem số tiền và nội dung chuyển khoản của đơn hàng, sau đó xác nhận thử nghiệm.</p>
            <a class="button space-top" href="transfer.php?booking_id=<?= $bookingId ?>">Hiển thị mã QR</a>
        </div>
        <details class="manual-payment space-top">
            <summary>Thanh toán thử nghiệm trực tiếp</summary>
            <p class="muted space-top">Chọn kết quả để kiểm tra luồng thanh toán khi không dùng QR.</p>
            <form method="post" action="pay.php" class="stack-actions space-top">
                <?php cbp_csrf_field(); ?>
                <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
                <input type="hidden" name="request_key" value="<?= cbp_e($requestKey) ?>">
                <button class="button" name="outcome" value="success" type="submit">Mô phỏng thanh toán thành công</button>
                <button class="button button-danger-outline" name="outcome" value="failed" type="submit">Mô phỏng thanh toán thất bại</button>
            </form>
        </details>
        <p class="payment-note">Hai phương thức đều mô phỏng, không phát sinh giao dịch tiền thật.</p>
    </section>
</div>
<?php cbp_footer(); ?>

