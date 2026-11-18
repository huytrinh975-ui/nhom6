<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
try {
    $customer = cbp_require_customer();
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        cbp_require_post();
        $bookingId = cbp_request_id('booking_id', $_POST);
        $attemptId = cbp_pay($bookingId, (int) $customer['id'],
            cbp_text($_POST['request_key'] ?? null, 64),
            cbp_text($_POST['outcome'] ?? null, 10), 'QR_DEMO',
            cbp_text($_POST['qr_token'] ?? null, 64));
        cbp_redirect('result.php?attempt_id=' . $attemptId);
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        throw new CBPUserError('Phương thức yêu cầu không được hỗ trợ.', 405);
    }
    $bookingId = cbp_request_id('booking_id', $_GET);
    $order = cbp_owned_order($bookingId, (int) $customer['id']);
    $seats = cbp_seats($bookingId);
    $issues = cbp_payment_issues($order, $seats);
    if ($issues) { throw new CBPUserError($issues[0], 409); }
    $quote = cbp_qr_quote($order, $seats, (int) $customer['id']);
} catch (Throwable $error) { cbp_handle_error($error); }
cbp_header('Chuyển khoản bằng mã QR', $customer, 'orders');
cbp_heading('Chuyển khoản bằng mã QR', 'Kiểm tra mã đơn, số tiền và nội dung trước khi xác nhận thử nghiệm.', $order['booking_code']);
?>
<div class="checkout-grid transfer-grid">
    <section class="panel qr-panel" aria-labelledby="qr-heading">
        <span class="pill info">CHUYỂN KHOẢN · THỬ NGHIỆM</span>
        <h2 id="qr-heading" class="space-top">Quét mã để xem thông tin đơn</h2>
        <p class="muted small">Dùng camera hoặc ứng dụng đọc mã QR trên điện thoại.</p>
        <div class="qr-frame">
            <img class="transfer-qr" src="qr-image.php?booking_id=<?= $bookingId ?>&amp;ref=<?= cbp_e($quote['reference']) ?>"
                width="340" height="340" alt="Mã QR thông tin đơn <?= cbp_e($order['booking_code']) ?>, chỉ dùng thử nghiệm">
        </div>
        <p class="qr-expiry">Hiệu lực đến <strong><?= cbp_e(date('H:i:s · d/m/Y', $quote['expires_at'])) ?></strong> (giờ Việt Nam).</p>
        <p class="small muted">Mỗi mã có thời hạn tối đa 10 phút. <a href="transfer.php?booking_id=<?= $bookingId ?>">Tải lại mã QR</a></p>
        <div class="status-notice warning space-top">
            <strong>Mã QR mô phỏng, không nhận tiền thật</strong>
            <p>Mã chứa thông tin đơn hàng, không có tài khoản ngân hàng. Quét mã không tự thanh toán hoặc tạo vé.</p>
        </div>
    </section>
    <section class="panel" aria-labelledby="transfer-heading">
        <h2 id="transfer-heading">Thông tin chuyển khoản</h2>
        <dl class="receipt transfer-receipt">
            <div><dt>Đơn vị nhận mô phỏng</dt><dd>CINEBOOK</dd></div>
            <div><dt>Mã đơn hàng</dt><dd><?= cbp_e($order['booking_code']) ?></dd></div>
            <div><dt>Nội dung chuyển khoản</dt><dd class="transfer-reference"><?= cbp_e($quote['reference']) ?></dd></div>
            <div><dt>Phim</dt><dd><?= cbp_e($order['movie_title']) ?></dd></div>
            <div><dt>Suất chiếu</dt><dd><?= cbp_e(cbp_date_label($order['start_time'])) ?></dd></div>
            <div><dt>Ghế</dt><dd><?= cbp_e(implode(', ', array_column($seats, 'seat_number'))) ?></dd></div>
        </dl>
        <div class="total-row"><span>Số tiền</span><strong><?= cbp_e(cbp_money(cbp_cents($order['total_amount']))) ?></strong></div>
        <p class="muted space-top">Sau khi kiểm tra thông tin, chọn kết quả thử nghiệm. Xác nhận thành công sẽ ghi nhận giao dịch và phát hành vé điện tử.</p>
        <form method="post" action="transfer.php" class="stack-actions space-top">
            <?php cbp_csrf_field(); ?>
            <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
            <input type="hidden" name="request_key" value="<?= cbp_e($quote['request_key']) ?>">
            <input type="hidden" name="qr_token" value="<?= cbp_e($quote['token']) ?>">
            <button class="button" type="submit" name="outcome" value="success">Xác nhận chuyển khoản thử nghiệm</button>
            <button class="button button-danger-outline" type="submit" name="outcome" value="failed">Thử giao dịch thất bại</button>
            <a class="text-link" href="pay.php?booking_id=<?= $bookingId ?>">Đổi phương thức thanh toán</a>
            <a class="text-link" href="checkout.php?booking_id=<?= $bookingId ?>">Quay lại đơn hàng</a>
        </form>
    </section>
</div>
<?php cbp_footer(); ?>
