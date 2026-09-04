<?php
$pageTitle = 'Košík';
require_once 'includes/header.php';

$cart = getCart();
$cartTotal = getCartTotal();
?>

<section class="page-header">
    <div class="container">
        <h1>Nákupný košík</h1>
        <nav class="breadcrumb">
            <a href="/">Domov</a>
            <span>/</span>
            <span>Košík</span>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($cart)): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <h3>Váš košík je prázdny</h3>
                <p>Prezrite si naše produkty a pridajte ich do košíka.</p>
                <a href="/produkty" class="btn btn--primary">Zobraziť produkty</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div class="cart-items" id="cartItems">
                    <div class="cart-table">
                        <div class="cart-table__header">
                            <span>Produkt</span>
                            <span>Cena</span>
                            <span>Množstvo</span>
                            <span>Spolu</span>
                            <span></span>
                        </div>
                        <?php foreach ($cart as $item): ?>
                            <div class="cart-item" data-id="<?= e($item['id']) ?>">
                                <div class="cart-item__product">
                                    <div class="cart-item__image">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                                        <?php else: ?>
                                            <div class="product-card__placeholder product-card__placeholder--sm">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="cart-item__info">
                                        <h4><a href="/produkt?id=<?= e($item['id']) ?>"><?= e($item['name']) ?></a></h4>
                                        <span class="cart-item__sku">Kód: <?= e($item['sku']) ?></span>
                                    </div>
                                </div>
                                <div class="cart-item__price" data-label="Cena:">
                                    <?= formatPrice($item['price']) ?>
                                </div>
                                <div class="cart-item__quantity" data-label="Množstvo:">
                                    <div class="quantity-input quantity-input--sm">
                                        <button class="quantity-input__btn" data-action="decrease" data-id="<?= e($item['id']) ?>">-</button>
                                        <input type="number" value="<?= $item['quantity'] ?>" min="1" data-id="<?= e($item['id']) ?>" class="cart-qty-input">
                                        <button class="quantity-input__btn" data-action="increase" data-id="<?= e($item['id']) ?>">+</button>
                                    </div>
                                </div>
                                <div class="cart-item__total" data-label="Spolu:">
                                    <?= formatPrice($item['price'] * $item['quantity']) ?>
                                </div>
                                <div class="cart-item__remove">
                                    <button class="btn-remove" data-id="<?= e($item['id']) ?>" aria-label="Odstrániť">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cart-summary">
                    <h3>Súhrn objednávky</h3>
                    <div class="cart-summary__row">
                        <span>Počet položiek</span>
                        <span id="summaryCount"><?= getCartCount() ?></span>
                    </div>
                    <div class="cart-summary__row cart-summary__row--total">
                        <span>Celkom s DPH</span>
                        <span id="summaryTotal"><?= formatPrice($cartTotal) ?></span>
                    </div>
                    <p class="cart-summary__note">K uvedenej sume bude pripočítané poštovné, ktoré bude stanovené individuálne podľa adresy doručenia. Tovar skladom zvyčajne doručujeme do druhého pracovného dňa.</p>
                    <a href="/objednavka" class="btn btn--primary btn--lg btn--block">Pokračovať v objednávke</a>
                    <a href="/produkty" class="btn btn--outline btn--block">Pokračovať v nákupe</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
