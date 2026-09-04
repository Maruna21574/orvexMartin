<?php
$pageTitle = 'Objednávka';
require_once 'includes/header.php';

$cart = getCart();
if (empty($cart)) {
    header('Location: /kosik');
    exit;
}

$errors = [];
$success = false;
$orderNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isSpamSubmission()) {
    // Tichy no-op pre spamovacich botov - predstierame uspech (aj s cislom
    // objednavky), aby si neuvedomili, ze boli odhaleni, ale v skutocnosti sa
    // nic neulozi ani neposle.
    $orderNumber = 'OBJ-' . date('Ymd') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    $success = true;
    clearCart();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $errors[] = 'Neplatný bezpečnostný token. Skúste to znova.';
    }

    $name = trim($_POST['name'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $ico = trim($_POST['ico'] ?? '');
    $dic = trim($_POST['dic'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $note = trim($_POST['note'] ?? '');

    if ($name === '') $errors[] = 'Zadajte meno a priezvisko.';
    if ($phone === '') $errors[] = 'Zadajte telefónne číslo.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Zadajte platný email.';
    if ($street === '') $errors[] = 'Zadajte ulicu a číslo.';
    if ($city === '') $errors[] = 'Zadajte mesto.';
    if ($zip === '') $errors[] = 'Zadajte PSČ.';

    if (empty($errors)) {
        $orderData = [
            'name' => $name,
            'company' => $company,
            'ico' => $ico,
            'dic' => $dic,
            'phone' => $phone,
            'email' => $email,
            'street' => $street,
            'city' => $city,
            'zip' => $zip,
            'note' => $note,
        ];

        $savedCart = $cart;
        $savedTotal = getCartTotal();

        // Objednávky sa negenerujú v MRP ani sa nefakturujú cez web - to rieši
        // admin ručne v inom systéme (evidencia + zmena stavu je v /admin).
        // Ak DB práve nie je dostupná, objednávka sa neulozi do admin panelu,
        // ale zakaznik napriek tomu dostane potvrdenie a cislo objednavky -
        // pri nízkom objeme tohto e-shopu je to prijatelne riziko oproti tomu,
        // aby checkout uplne zlyhal.
        try {
            $order = createOrder($orderData, $savedCart, $savedTotal);
            $orderNumber = $order['order_number'];
        } catch (\Throwable $e) {
            if (DEBUG_MODE) {
                error_log('Ulozenie objednavky zlyhalo: ' . $e->getMessage());
            }
            $orderNumber = 'OBJ-' . date('Ymd') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        }

        $success = true;
        clearCart();

        sendOrderConfirmation($orderNumber, $orderData, $savedCart, $savedTotal);
    }
}

$csrfToken = generateCsrfToken();
?>

<section class="page-header">
    <div class="container">
        <h1>Objednávka</h1>
        <nav class="breadcrumb">
            <a href="/">Domov</a>
            <span>/</span>
            <a href="/kosik">Košík</a>
            <span>/</span>
            <span>Objednávka</span>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($success): ?>
            <div class="order-success">
                <div class="order-success__icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h2>Objednávka bola odoslaná</h2>
                <p class="order-success__number">Číslo objednávky: <strong><?= e($orderNumber) ?></strong></p>
                <p>Ďakujeme za vašu objednávku. Faktúru vám zašleme na uvedený email. V prípade otázok nás neváhajte kontaktovať.</p>
                <div class="order-success__actions">
                    <a href="/" class="btn btn--primary">Späť na hlavnú stránku</a>
                    <a href="/produkty" class="btn btn--outline">Pokračovať v nákupe</a>
                </div>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert--error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <div>
                        <?php foreach ($errors as $error): ?>
                            <p><?= e($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="order-layout" id="orderForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <?= renderAntiSpamFields() ?>

                <div class="order-form">
                    <div class="form-section">
                        <h3>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Kontaktné údaje
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Meno a priezvisko <span class="required">*</span></label>
                                <input type="text" id="name" name="name" required value="<?= e($name ?? '') ?>" placeholder="Ján Novák">
                            </div>
                            <div class="form-group">
                                <label for="company">Názov firmy</label>
                                <input type="text" id="company" name="company" value="<?= e($company ?? '') ?>" placeholder="Firma s.r.o.">
                            </div>
                            <div class="form-group">
                                <label for="ico">IČO</label>
                                <input type="text" id="ico" name="ico" value="<?= e($ico ?? '') ?>" placeholder="12345678">
                            </div>
                            <div class="form-group">
                                <label for="dic">DIČ / IČ DPH</label>
                                <input type="text" id="dic" name="dic" value="<?= e($dic ?? '') ?>" placeholder="SK1234567890">
                            </div>
                            <div class="form-group">
                                <label for="phone">Telefón <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" required value="<?= e($phone ?? '') ?>" placeholder="+421 9xx xxx xxx">
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" required value="<?= e($email ?? '') ?>" placeholder="jan@firma.sk">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Adresa doručenia
                        </h3>
                        <div class="form-grid">
                            <div class="form-group form-group--full">
                                <label for="street">Ulica a číslo <span class="required">*</span></label>
                                <input type="text" id="street" name="street" required value="<?= e($street ?? '') ?>" placeholder="Hlavná 1">
                            </div>
                            <div class="form-group">
                                <label for="city">Mesto <span class="required">*</span></label>
                                <input type="text" id="city" name="city" required value="<?= e($city ?? '') ?>" placeholder="Martin">
                            </div>
                            <div class="form-group">
                                <label for="zip">PSČ <span class="required">*</span></label>
                                <input type="text" id="zip" name="zip" required value="<?= e($zip ?? '') ?>" placeholder="036 01" maxlength="6">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            Poznámka k objednávke
                        </h3>
                        <div class="form-group">
                            <textarea id="note" name="note" rows="3" placeholder="Vaša poznámka..."><?= e($note ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="order-summary">
                    <h3>Vaša objednávka</h3>
                    <div class="order-summary__items">
                        <?php foreach ($cart as $item): ?>
                            <div class="order-summary__item">
                                <div class="order-summary__item-info">
                                    <span class="order-summary__item-name"><?= e($item['name']) ?></span>
                                    <span class="order-summary__item-qty"><?= $item['quantity'] ?>x</span>
                                </div>
                                <span class="order-summary__item-price"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-summary__total">
                        <span>Celkom s DPH</span>
                        <span><?= formatPrice(getCartTotal()) ?></span>
                    </div>
                    <p class="order-summary__note">K uvedenej sume bude pripočítané poštovné, ktoré bude stanovené individuálne. Tovar skladom zvyčajne doručujeme do druhého pracovného dňa. Faktúra bude zaslaná na uvedený email.</p>
                    <button type="submit" class="btn btn--primary btn--lg btn--block">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Odoslať objednávku
                    </button>
                    <p class="order-summary__legal">Odoslaním objednávky súhlasíte so spracovaním údajov za účelom vybavenia objednávky.</p>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
