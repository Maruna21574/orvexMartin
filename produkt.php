<?php
require_once 'config.php';

$productId = $_GET['id'] ?? '';
if ($productId === '') {
    header('Location: /produkty');
    exit;
}

$product = getProduct($productId);
if (!$product) {
    header('Location: /produkty');
    exit;
}

$pageTitle = $product['name'];
$pageDescription = mb_substr(strip_tags($product['description']), 0, 160);
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="/">Domov</a>
            <span>/</span>
            <a href="/produkty">Produkty</a>
            <span>/</span>
            <?php if ($product['category']): ?>
                <a href="/produkty?kategoria=<?= e($product['category_id']) ?>"><?= e($product['category']) ?></a>
                <span>/</span>
            <?php endif; ?>
            <span><?= e($product['name']) ?></span>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="product-detail">
            <div class="product-detail__gallery">
                <div class="product-detail__main-image" id="mainImage">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
                    <?php else: ?>
                        <div class="product-card__placeholder product-card__placeholder--lg">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($product['images'])): ?>
                    <div class="product-detail__thumbs">
                        <?php foreach ($product['images'] as $i => $img): ?>
                            <button class="product-detail__thumb <?= $i === 0 ? 'active' : '' ?>"
                                    data-image="<?= e($img) ?>">
                                <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?> - foto <?= $i + 1 ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-detail__info">
                <span class="product-detail__category"><?= e($product['category']) ?></span>
                <h1><?= e($product['name']) ?></h1>
                <p class="product-detail__sku">Kód produktu: <strong><?= e($product['sku']) ?></strong></p>

                <div class="product-detail__price-box">
                    <span class="product-detail__price"><?= formatPrice($product['price_vat']) ?></span>
                    <span class="product-detail__price-net">bez DPH: <?= formatPrice($product['price']) ?></span>
                    <span class="product-detail__vat">DPH <?= $product['vat_rate'] ?>%</span>
                </div>

                <div class="product-detail__stock <?= $product['stock'] > 0 ? 'product-detail__stock--in' : 'product-detail__stock--out' ?>">
                    <?php if ($product['stock'] > 0): ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Skladom (<?= $product['stock'] ?> <?= e($product['unit']) ?>)
                    <?php else: ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Na objednávku
                    <?php endif; ?>
                </div>

                <div class="product-detail__actions">
                    <div class="quantity-input">
                        <button class="quantity-input__btn" data-action="decrease" aria-label="Znížiť množstvo">-</button>
                        <input type="number" id="productQty" value="1" min="1" max="<?= $product['stock'] ?: 999 ?>">
                        <button class="quantity-input__btn" data-action="increase" aria-label="Zvýšiť množstvo">+</button>
                    </div>
                    <button class="btn btn--primary btn--lg btn--add-to-cart"
                            data-id="<?= e($product['id']) ?>"
                            data-name="<?= e($product['name']) ?>"
                            data-price="<?= $product['price_vat'] ?>"
                            data-image="<?= e($product['image']) ?>"
                            data-sku="<?= e($product['sku']) ?>"
                            data-qty-input="productQty">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Pridať do košíka
                    </button>
                </div>

                <div class="product-detail__trust">
                    <div class="product-detail__trust-item">
                        <span class="product-detail__trust-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        </span>
                        <span>Doprava po celom Slovensku</span>
                    </div>
                    <div class="product-detail__trust-item">
                        <span class="product-detail__trust-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </span>
                        <span>35+ rokov skúseností na trhu</span>
                    </div>
                    <div class="product-detail__trust-item">
                        <span class="product-detail__trust-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <span>Odborné poradenstvo k výberu</span>
                    </div>
                </div>

                <div class="product-detail__params">
                    <h3>Informácie o produkte</h3>
                    <table>
                        <?php if ($product['category']): ?>
                            <tr>
                                <td>Kategória</td>
                                <td><?= e($product['category']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Kód produktu</td>
                            <td><?= e($product['sku']) ?></td>
                        </tr>
                        <tr>
                            <td>Merná jednotka</td>
                            <td><?= e($product['unit']) ?></td>
                        </tr>
                        <?php foreach ($product['params'] as $param): ?>
                            <tr>
                                <td><?= e($param['name']) ?></td>
                                <td><?= e($param['value']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($product['description'])): ?>
            <div class="product-description">
                <h2>Popis produktu</h2>
                <div class="product-description__content">
                    <?= nl2br(e($product['description'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$relatedProducts = array_filter(getProducts(['category' => $product['category_id']]), function ($p) use ($product) {
    return $p['id'] !== $product['id'];
});
$relatedProducts = array_slice(array_values($relatedProducts), 0, 4);
?>
<?php if (!empty($relatedProducts)): ?>
<section class="section section--alt">
    <div class="container">
        <div class="section__header">
            <h2>Podobné produkty</h2>
            <p>Ďalšie produkty z kategórie <?= e($product['category']) ?></p>
        </div>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $rp): ?>
                <div class="product-card">
                    <a href="/produkt?id=<?= e($rp['id']) ?>" class="product-card__image">
                        <?php if (!empty($rp['image'])): ?>
                            <img src="<?= e($rp['image']) ?>" alt="<?= e($rp['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="product-card__placeholder">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        <?php endif; ?>
                        <?php if ($rp['stock'] > 0): ?>
                            <span class="product-card__badge product-card__badge--stock">Skladom</span>
                        <?php endif; ?>
                    </a>
                    <div class="product-card__body">
                        <span class="product-card__category"><?= e($rp['category']) ?></span>
                        <h3><a href="/produkt?id=<?= e($rp['id']) ?>"><?= e($rp['name']) ?></a></h3>
                        <p class="product-card__sku">Kód: <?= e($rp['sku']) ?></p>
                    </div>
                    <div class="product-card__footer">
                        <div class="product-card__price">
                            <span class="product-card__price-vat"><?= formatPrice($rp['price_vat']) ?></span>
                            <span class="product-card__price-net">bez DPH: <?= formatPrice($rp['price']) ?></span>
                        </div>
                        <button class="btn btn--primary btn--sm btn--add-to-cart"
                                data-id="<?= e($rp['id']) ?>"
                                data-name="<?= e($rp['name']) ?>"
                                data-price="<?= $rp['price_vat'] ?>"
                                data-image="<?= e($rp['image']) ?>"
                                data-sku="<?= e($rp['sku']) ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="product-inquiry">
            <div class="product-inquiry__info">
                <h2>Máte otázku k tomuto produktu?</h2>
                <p>Neváhajte nás kontaktovať. Radi vám poradíme s výberom, poskytneme cenovú ponuku alebo zodpovieme vaše technické otázky.</p>
                <div class="product-inquiry__contacts">
                    <a href="tel:+421905500950" class="product-inquiry__link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        0905 500 950
                    </a>
                    <a href="mailto:orvex@orvex.sk" class="product-inquiry__link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        orvex@orvex.sk
                    </a>
                </div>
            </div>
            <form class="product-inquiry__form" id="contactForm">
                <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                <input type="hidden" name="subject" value="dopyt">
                <div class="form-group">
                    <input type="text" name="name" required placeholder="Meno a priezvisko *">
                </div>
                <div class="product-inquiry__row">
                    <div class="form-group">
                        <input type="email" name="email" required placeholder="E-mail *">
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" placeholder="Telefón">
                    </div>
                </div>
                <div class="form-group">
                    <textarea name="message" rows="3" required placeholder="Vaša otázka k produktu <?= e($product['name']) ?> (<?= e($product['sku']) ?>)..."><?= "Dobrý deň, mám záujem o produkt " . e($product['name']) . " (" . e($product['sku']) . "). " ?></textarea>
                </div>
                <button type="submit" class="btn btn--primary btn--block" id="contactSubmitBtn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Odoslať dopyt
                </button>
            </form>
        </div>
    </div>
</section>

<div class="sticky-cta" id="stickyCta">
    <div class="container">
        <div class="sticky-cta__inner">
            <div class="sticky-cta__info">
                <span class="sticky-cta__name"><?= e($product['name']) ?></span>
                <span class="sticky-cta__price"><?= formatPrice($product['price_vat']) ?></span>
            </div>
            <button class="btn btn--primary btn--add-to-cart"
                    data-id="<?= e($product['id']) ?>"
                    data-name="<?= e($product['name']) ?>"
                    data-price="<?= $product['price_vat'] ?>"
                    data-image="<?= e($product['image']) ?>"
                    data-sku="<?= e($product['sku']) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Pridať do košíka
            </button>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
