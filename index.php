<?php
$pageTitle = 'Predaj LKT a náhradné diely LKT (NDLKT)';
$pageDescription = 'Predaj LKT a náhradné diely LKT (NDLKT) od roku 1991. Lesné kolesové traktory, snehové reťaze, hydraulické čerpadlá a lanové úväzky. Doprava po celom Slovensku.';
require_once 'includes/header.php';

$categories = getCategories();
$allProducts = getProducts();
$featuredByCategory = [];

foreach ($allProducts as $product) {
    if (empty($product['image'])) continue;

    $categoryId = mb_strtolower(trim((string) ($product['category_id'] ?? '')));
    $categoryName = mb_strtolower(trim((string) ($product['category'] ?? '')));
    $isBearing = str_contains($categoryId, 'loz')
        || str_contains($categoryName, 'lož')
        || str_contains($categoryName, 'loz');

    if ($isBearing) continue;

    $categoryKey = $categoryId !== '' ? $categoryId : $categoryName;
    if ($categoryKey === '') continue;
    $featuredByCategory[$categoryKey][] = $product;
}

// Vyberáme po jednom produkte z každej kategórie, aby slider nepôsobil ako
// zoznam rovnakých položiek. Po prvom kole pokračujeme ďalšími položkami.
$featuredProducts = [];
$categoryPositions = array_fill_keys(array_keys($featuredByCategory), 0);

while (count($featuredProducts) < 24) {
    $addedProduct = false;

    foreach ($featuredByCategory as $categoryKey => $categoryProducts) {
        $position = $categoryPositions[$categoryKey];
        if (!isset($categoryProducts[$position])) continue;

        $featuredProducts[] = $categoryProducts[$position];
        $categoryPositions[$categoryKey]++;
        $addedProduct = true;

        if (count($featuredProducts) >= 24) break;
    }

    if (!$addedProduct) break;
}

if ($featuredProducts === []) {
    $featuredProducts = array_slice($allProducts, 0, 24);
}
?>

<section class="hero" id="heroSection">
    <div class="hero__glow" id="heroGlow"></div>
    <div class="hero__shapes">
        <div class="hero__shape hero__shape--1"></div>
        <div class="hero__shape hero__shape--2"></div>
        <div class="hero__shape hero__shape--3"></div>
    </div>
    <div class="container hero__grid">
        <div class="hero__content">
            <span class="hero__badge hero-anim" style="--delay:0">Od roku 1991</span>
            <h1 class="hero-anim hero__title" style="--delay:1">Profesionálna lesná technika a&nbsp;príslušenstvo</h1>
            <p class="hero-anim" style="--delay:2">Predaj LKT a lesnej kolesovej techniky, náhradné diely LKT (NDLKT), snehové reťaze, hydraulické čerpadlá a lanové úväzky pre profesionálov v&nbsp;lesnom hospodárstve.</p>
            <div class="hero__actions hero-anim" style="--delay:3">
                <a href="/produkty" class="btn btn--white btn--lg">Zobraziť produkty</a>
                <a href="#kategorie" class="btn btn--outline btn--lg">Kategórie</a>
            </div>
            <div class="hero__quickfacts hero-anim" style="--delay:4">
                <span><strong>35+</strong> rokov skúseností</span>
                <span><strong>Odborné</strong> poradenstvo</span>
                <span><strong>Kompletný</strong> sortiment</span>
            </div>
        </div>

    </div>
</section>

<section class="section home-story">
    <div class="container home-story__grid">
        <div class="home-story__media">
            <img src="/assets/images/hero-bg.jpg" alt="Európsky les pri horskom jazere" loading="lazy">
        </div>
        <div class="home-story__content">
            <span class="section__eyebrow">Viac než e-shop</span>
            <h2>Riešenia pre prácu v lese na jednom mieste</h2>
            <p>Pomáhame profesionálom vybrať techniku, diely a vybavenie podľa skutočného spôsobu použitia. Od bežnej náhrady až po riešenie pre náročné podmienky.</p>
            <div class="home-story__points">
                <div class="home-story__point">
                    <span class="home-story__point-icon">01</span>
                    <div>
                        <strong>Technika a príslušenstvo</strong>
                        <span>Prehľad sortimentu pre lesné hospodárstvo.</span>
                    </div>
                </div>
                <div class="home-story__point">
                    <span class="home-story__point-icon">02</span>
                    <div>
                        <strong>Diely podľa katalógového čísla</strong>
                        <span>Vyhľadajte vhodný diel rýchlo a presne.</span>
                    </div>
                </div>
                <div class="home-story__point">
                    <span class="home-story__point-icon">03</span>
                    <div>
                        <strong>Odborné poradenstvo</strong>
                        <span>Keď si nie ste istí výberom, ozvite sa nám.</span>
                    </div>
                </div>
            </div>
            <a href="/o-nas" class="btn btn--outline">Spoznajte ORVEX</a>
        </div>
    </div>
</section>

<section class="section" id="kategorie">
    <div class="container">
        <div class="section__header">
            <h2>Kategórie produktov</h2>
            <p>Kompletný sortiment pre lesnú techniku</p>
        </div>
        <div class="categories-grid">
            <?php
            $icons = [
                'lesna-technika' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="12" width="22" height="9" rx="1"/><path d="M3 12V7a1 1 0 0 1 1-1h4l2 3h10a1 1 0 0 1 1 1v2"/><circle cx="7" cy="21" r="2"/><circle cx="17" cy="21" r="2"/></svg>',
                'hydraulicke-cerpadla' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
                'snehove-retaze' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
                'lanove-uvazky' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>',
                'nahradne-diely' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
            ];
            foreach ($categories as $cat): ?>
                <a href="/produkty?kategoria=<?= e($cat['id']) ?>" class="category-card">
                    <div class="category-card__image">
                        <?php if (!empty($cat['image'])): ?>
                            <img src="<?= e($cat['image']) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <?= $icons[$cat['id']] ?? $icons['nahradne-diely'] ?>
                        <?php endif; ?>
                    </div>
                    <div class="category-card__body">
                        <h3><?= e($cat['name']) ?></h3>
                        <span class="category-card__count"><?= $cat['count'] ?> produktov</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--alt home-paths">
    <div class="container">
        <div class="section__header">
            <span class="section__eyebrow">Ako vám vieme pomôcť</span>
            <h2>Vyberte si ďalší krok</h2>
            <p>Či už hľadáte konkrétny diel, nové vybavenie alebo radu, dostanete sa k nej na pár kliknutí.</p>
        </div>
        <div class="home-paths__grid">
            <a href="/produkty" class="home-path home-path--products">
                <span class="home-path__number">01</span>
                <span class="home-path__label">E-shop</span>
                <h3>Prehľadať sortiment</h3>
                <p>Produkty, náhradné diely a príslušenstvo pre lesnú techniku.</p>
                <span class="home-path__arrow" aria-hidden="true">→</span>
            </a>
            <a href="/kontakt" class="home-path home-path--advice">
                <span class="home-path__number">02</span>
                <span class="home-path__label">Poradenstvo</span>
                <h3>Potrebujete pomôcť s výberom?</h3>
                <p>Napíšte nám a pripravíme odporúčanie podľa vašej práce.</p>
                <span class="home-path__arrow" aria-hidden="true">→</span>
            </a>
            <a href="/o-nas" class="home-path home-path--company">
                <span class="home-path__number">03</span>
                <span class="home-path__label">O spoločnosti</span>
                <h3>Spoznajte naše skúsenosti</h3>
                <p>Prečítajte si viac o ORVEX, našej technike a službách.</p>
                <span class="home-path__arrow" aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>

<section class="section section--alt">
    <div class="container">
        <div class="section__header">
            <h2>Vybrané produkty</h2>
            <p>Najobľúbenejšie položky z nášho sortimentu</p>
        </div>
        <div class="products-slider" data-products-slider>
            <button type="button" class="products-slider__nav products-slider__nav--prev" data-products-prev aria-label="Predchádzajúce produkty">←</button>
            <div class="products-grid products-slider__track" data-products-track>
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <a href="/produkt?id=<?= e($product['id']) ?>" class="product-card__image">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="product-card__placeholder">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="product-card__badge product-card__badge--stock">Skladom</span>
                        <?php endif; ?>
                    </a>
                    <div class="product-card__body">
                        <span class="product-card__category"><?= e($product['category']) ?></span>
                        <h3><a href="/produkt?id=<?= e($product['id']) ?>"><?= e($product['name']) ?></a></h3>
                        <p class="product-card__sku">Kód: <?= e($product['sku']) ?></p>
                    </div>
                    <div class="product-card__footer">
                        <?php if ($product['stock'] > 0): ?>
                            <div class="product-card__price">
                                <span class="product-card__price-vat"><?= formatPrice($product['price_vat']) ?></span>
                                <span class="product-card__price-net">bez DPH: <?= formatPrice($product['price']) ?></span>
                            </div>
                            <button class="btn btn--primary btn--sm btn--add-to-cart"
                                    data-id="<?= e($product['id']) ?>"
                                    data-name="<?= e($product['name']) ?>"
                                    data-price="<?= $product['price_vat'] ?>"
                                    data-image="<?= e($product['image']) ?>"
                                    data-sku="<?= e($product['sku']) ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            </button>
                        <?php else: ?>
                            <div class="product-card__price">
                                <span class="product-card__price-vat product-card__price-vat--unavailable">Cena na vyžiadanie</span>
                            </div>
                            <a href="/produkt?id=<?= e($product['id']) ?>#dopyt" class="btn btn--outline btn--sm">Vyžiadať cenu</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            <button type="button" class="products-slider__nav products-slider__nav--next" data-products-next aria-label="Ďalšie produkty">→</button>
        </div>
        <div class="section__footer">
            <a href="/produkty" class="btn btn--outline">Zobraziť všetky produkty</a>
        </div>
    </div>
</section>

<section class="cta-banner">
    <div class="container">
        <div class="cta-banner__content">
            <h2>Potrebujete poradiť s výberom techniky?</h2>
            <p>Naši odborníci vám pomôžu vybrať optimálne riešenie pre vaše potreby. Kontaktujte nás pre nezáväznú konzultáciu.</p>
            <div class="cta-banner__actions">
                <a href="tel:+421905500950" class="btn btn--white btn--lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    0905 500 950
                </a>
                <a href="/kontakt" class="btn btn--cta-secondary btn--lg">Napíšte nám</a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__header">
            <h2>Prečo ORVEX?</h2>
            <p>Váš spoľahlivý partner pre profesionálne riešenia</p>
        </div>
        <div class="features">
            <div class="feature">
                <div class="feature__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>35+ rokov skúseností</h3>
                <p>Na trhu od roku 1991 s profesionálnou lesnou technikou</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <h3>Dodanie po celom SR</h3>
                <p>Tovar skladom zvyčajne doručíme do druhého pracovného dňa</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3>Overená kvalita</h3>
                <p>Certifikované produkty spĺňajúce európske normy</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <h3>Odborné poradenstvo</h3>
                <p>Pomôžeme vám vybrať optimálne riešenie pre vaše potreby</p>
            </div>
        </div>
    </div>
</section>

<?php
$contactFormTitle = 'Máte otázku?';
$contactFormDesc = 'Neváhajte nás kontaktovať. Radi vám poradíme s výberom techniky alebo náhradných dielov.';
$contactFormAlt = true;
require_once 'includes/contact-form.php';
?>

<?php require_once 'includes/footer.php'; ?>
