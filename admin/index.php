<?php
$pageTitle = 'Objednávky';
require_once __DIR__ . '/includes/header.php';

$statusFilter = $_GET['status'] ?? '';
if (!in_array($statusFilter, ORDER_STATUSES, true)) {
    $statusFilter = '';
}

$orders = getOrders(['status' => $statusFilter]);
?>

<h1>Objednávky</h1>

<div class="admin-tabs">
    <a href="/admin" class="admin-tabs__item<?= $statusFilter === '' ? ' admin-tabs__item--active' : '' ?>">Všetky</a>
    <?php foreach (ORDER_STATUS_LABELS as $statusKey => $statusLabel): ?>
        <a href="/admin?status=<?= e($statusKey) ?>" class="admin-tabs__item<?= $statusFilter === $statusKey ? ' admin-tabs__item--active' : '' ?>"><?= e($statusLabel) ?></a>
    <?php endforeach; ?>
</div>

<?php if (empty($orders)): ?>
    <p class="admin-empty">Žiadne objednávky v tomto filtri.</p>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Číslo objednávky</th>
                    <th>Dátum</th>
                    <th>Zákazník</th>
                    <th>Suma</th>
                    <th>Stav</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><a href="/admin/order?id=<?= (int) $order['id'] ?>"><?= e($order['order_number']) ?></a></td>
                        <td><?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></td>
                        <td><?= e($order['name']) ?></td>
                        <td><?= formatPrice((float) $order['total']) ?></td>
                        <td><span class="badge badge--<?= e($order['status']) ?>"><?= e(ORDER_STATUS_LABELS[$order['status']] ?? $order['status']) ?></span></td>
                        <td><a href="/admin/order?id=<?= (int) $order['id'] ?>" class="btn btn--outline btn--sm">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
