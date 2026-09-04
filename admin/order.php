<?php
$pageTitle = 'Detail objednávky';
require_once __DIR__ . '/includes/header.php';

$id = (int) ($_GET['id'] ?? 0);
$order = $id > 0 ? getOrder($id) : null;

if ($order === null) {
    http_response_code(404);
    echo '<h1>Objednávka nenájdená</h1><p><a href="/admin">Späť na zoznam objednávok</a></p>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$statusChangeError = '';
$statusChanged = isset($_GET['updated']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $newStatus = $_POST['status'] ?? '';
    $note = trim($_POST['note'] ?? '');

    if (!verifyCsrfToken($token)) {
        $statusChangeError = 'Neplatný bezpečnostný token. Skúste to znova.';
    } elseif (!in_array($newStatus, getValidNextStatuses($order['status']), true)) {
        $statusChangeError = 'Neplatný prechod stavu.';
    } else {
        if (updateOrderStatus($id, $newStatus)) {
            sendOrderStatusEmail($order, $newStatus, $note);
            header('Location: /admin/order?id=' . $id . '&updated=1');
            exit;
        }
        $statusChangeError = 'Zmena stavu sa nepodarila. Skúste to znova.';
    }

    // Nacitaj aktualny (nezmeneny) stav znova, aby sa formular zobrazil spravne po chybe.
    $order = getOrder($id);
}

$csrfToken = generateCsrfToken();
$nextStatuses = getValidNextStatuses($order['status']);
?>

<p><a href="/admin">&larr; Späť na zoznam objednávok</a></p>

<h1>Objednávka <?= e($order['order_number']) ?></h1>
<p><span class="badge badge--<?= e($order['status']) ?>"><?= e(ORDER_STATUS_LABELS[$order['status']] ?? $order['status']) ?></span></p>

<?php if ($statusChanged): ?>
    <div class="alert alert--success">
        <p>Stav objednávky bol zmenený a zákazníkovi bol odoslaný e-mail.</p>
    </div>
<?php endif; ?>

<?php if ($statusChangeError !== ''): ?>
    <div class="alert alert--error">
        <p><?= e($statusChangeError) ?></p>
    </div>
<?php endif; ?>

<div class="order-detail__grid">
    <div class="order-detail__section">
        <h3>Zákazník</h3>
        <p>
            <?= e($order['name']) ?><br>
            <?php if (!empty($order['company'])): ?><?= e($order['company']) ?><br><?php endif; ?>
            <?php if (!empty($order['ico'])): ?>IČO: <?= e($order['ico']) ?><?php if (!empty($order['dic'])): ?> / DIČ: <?= e($order['dic']) ?><?php endif; ?><br><?php endif; ?>
            <?= e($order['email']) ?><br>
            <?= e($order['phone']) ?>
        </p>
    </div>

    <div class="order-detail__section">
        <h3>Adresa doručenia</h3>
        <p>
            <?= e($order['street']) ?><br>
            <?= e($order['zip']) ?> <?= e($order['city']) ?>
        </p>
    </div>

    <?php if (!empty($order['note'])): ?>
        <div class="order-detail__section">
            <h3>Poznámka zákazníka</h3>
            <p><?= nl2br(e($order['note'])) ?></p>
        </div>
    <?php endif; ?>
</div>

<h3>Položky</h3>
<div class="admin-table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Produkt</th>
                <th>Kód</th>
                <th>Množstvo</th>
                <th>Cena / ks</th>
                <th>Spolu</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= e($item['sku']) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td><?= formatPrice((float) $item['price']) ?></td>
                    <td><?= formatPrice((float) $item['price'] * (int) $item['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Celkom</strong></td>
                <td><strong><?= formatPrice((float) $order['total']) ?></strong></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php if (!empty($nextStatuses)): ?>
    <h3>Zmena stavu</h3>
    <form method="POST" class="order-detail__status-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <div class="form-group">
            <label for="status">Nový stav</label>
            <select id="status" name="status" required>
                <option value="">Vyberte stav...</option>
                <?php foreach ($nextStatuses as $statusKey): ?>
                    <option value="<?= e($statusKey) ?>"><?= e(ORDER_STATUS_LABELS[$statusKey]) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="note">Poznámka pre zákazníka (voliteľné, napr. dopravca/číslo zásielky alebo dôvod zrušenia)</label>
            <textarea id="note" name="note" rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn--primary">Zmeniť stav a odoslať e-mail</button>
    </form>
<?php else: ?>
    <p class="admin-empty">Táto objednávka je vo finálnom stave, ďalšiu zmenu nie je možné vykonať.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
