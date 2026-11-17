<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
try {
    $customer = cbp_require_customer();
    $ticket = cbp_owned_ticket(cbp_request_id('id', $_GET), (int) $customer['id']);
    $snapshot = cbp_snapshot($ticket);
    $order = cbp_owned_order((int) $ticket['booking_id'], (int) $customer['id']);
    $cancelInfo = cbp_cancel_info($order, $ticket);
} catch (Throwable $error) { cbp_handle_error($error); }
cbp_header('Vé điện tử', $customer, 'tickets');
cbp_heading('Vé điện tử của bạn', 'Thông tin được lưu tại thời điểm thanh toán thành công.', $ticket['ticket_code']);
?>
<div class="ticket-wrap"><?php cbp_ticket_markup($ticket, $snapshot); ?><div class="inline-actions no-print space-top"><a class="button" href="print-ticket.php?id=<?= (int) $ticket['id'] ?>" target="_blank" rel="noopener">Mở bản in vé</a><a class="button button-secondary" href="download-ticket.php?id=<?= (int) $ticket['id'] ?>">Tải vé HTML</a><a class="text-link" href="checkout.php?booking_id=<?= (int) $ticket['booking_id'] ?>">Xem đơn hàng</a><?php if ($cancelInfo['allowed']): ?><a class="text-link danger-text" href="cancel.php?booking_id=<?= (int) $ticket['booking_id'] ?>">Hủy vé</a><?php endif; ?></div><p class="muted small no-print space-top">Chọn “Mở bản in vé”, nhấn Ctrl + P và chọn “Save as PDF” để lưu vé dưới dạng PDF.</p></div>
<?php cbp_footer(); ?>

