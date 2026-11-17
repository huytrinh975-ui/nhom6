<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

try {
    $customer = cbp_require_customer();
    $ticket = cbp_owned_ticket(cbp_request_id('id', $_GET), (int) $customer['id']);
    $snapshot = cbp_snapshot($ticket);
} catch (Throwable $error) {
    cbp_handle_error($error);
}

cbp_header('In vé điện tử', $customer, 'tickets');
?>
<section class="panel no-print space-top">
    <h1>In vé điện tử / Lưu PDF</h1>
    <p class="muted">Nhấn <strong>Ctrl + P</strong> (Windows/Linux) hoặc <strong>Command + P</strong> (macOS). Chọn máy in, hoặc chọn “Save as PDF” để lưu vé.</p>
    <p class="space-top"><a href="ticket.php?id=<?= (int) $ticket['id'] ?>">Trở lại trang vé</a></p>
</section>
<div class="ticket-wrap space-top">
    <?php cbp_ticket_markup($ticket, $snapshot); ?>
</div>
<?php cbp_footer(); ?>
