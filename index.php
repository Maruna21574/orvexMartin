<?php
$pageTitle = 'Náhradné diely na LKT a predaj LKT (NDLKT)';
$pageDescription = 'Náhradné diely na LKT (NDLKT) a predaj LKT od roku 1991. Lesné kolesové traktory, snehové reťaze, hydraulické čerpadlá a lanové úväzky. Doprava po celom Slovensku.';
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

<div class="hslider" data-hslider aria-roledescription="carousel" aria-label="Hlavná ponuka ORVEX">
    <div class="hslider__track" data-hslider-track>
        <div class="hslider__slide" data-hslide aria-roledescription="slide" aria-label="1 z 3">
            <section class="hslide hslide--veriga" style="background-image:url('/assets/images/veriga/hero-snow.webp')">
                <div class="container hslide__inner">
                    <img src="/assets/images/veriga/veriga-logo.png" alt="VERIGA since 1922" class="hslide__logo" width="220" height="51">
                    <span class="hslide__badge">Zástupca VERIGA pre Slovensko</span>
                    <h2 class="hslide__title">Snehové reťaze pre autá, pracovné a lesné stroje</h2>
                    <p>Osobné a nákladné autá, traktory, pracovné aj lesné stroje. Povedzte nám pneumatiky a stroj, pripravíme vám cenovú ponuku.</p>
                    <div class="hero__actions">
                        <a href="/snehove-retaze-veriga" data-goto="dopyt" class="btn btn--primary btn--lg">Vyžiadať cenovú ponuku</a>
                        <a href="/snehove-retaze-veriga" data-goto="typy" class="btn btn--outline btn--lg">Dopyt na reťaze</a>
                    </div>
                </div>
            </section>
        </div>

<div class="hslider__slide" data-hslide aria-roledescription="slide" aria-label="2 z 3">
            <section class="hslide hslide--lkt" style="background-image:linear-gradient(90deg,rgba(8,30,18,.92) 0%,rgba(10,40,24,.72) 38%,rgba(10,40,24,0) 68%),url('/assets/images/hero-lkt-klest.webp')">
                <div class="container hslide__inner">
                    <span class="hero__badge">Lesná kolesová technika</span>
                    <h1 class="hslide__title">Predaj LKT</h1>
                    <p>Lesné kolesové traktory LKT a lesná kolesová technika pre profesionálov v lesnom hospodárstve. Poradíme s výberom stroja a dodáme aj náhradné diely.</p>
                    <div class="hero__actions">
                        <a href="/kontakt" class="btn btn--white btn--lg">Kontaktujte nás</a>
                        <a href="/produkty" class="btn btn--outline btn--lg">Náhradné diely LKT</a>
                    </div>
                </div>
            </section>
        </div>

<div class="hslider__slide" data-hslide aria-roledescription="slide" aria-label="3 z 3">
            <section class="hslide hslide--diely" style="background-image:linear-gradient(90deg,rgba(8,30,18,.94) 0%,rgba(10,40,24,.78) 36%,rgba(10,40,24,.1) 70%),url('/assets/images/hero-diely.webp')">
                <div class="container hslide__inner">
                    <span class="hero__badge">Predaj LKT</span>
                    <h2 class="hslide__title">Náhradné diely na LKT (NDLKT)</h2>
                    <p>Ložiská, filtre, brzdy, hydraulika, hriadele, tesnenia a ďalšie náhradné diely na LKT, lesné kolesové traktory. Vyhľadajte podľa názvu alebo katalógového čísla, doprava po celom Slovensku.</p>
                    <div class="hero__actions">
                        <a href="/produkty" class="btn btn--white btn--lg">Zobraziť produkty</a>
                        <a href="#kategorie" class="btn btn--outline btn--lg">Kategórie dielov</a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <button type="button" class="hslider__nav hslider__nav--prev" data-hslider-prev aria-label="Predchádzajúci slide">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button type="button" class="hslider__nav hslider__nav--next" data-hslider-next aria-label="Ďalší slide">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>
    </button>
    <div class="hslider__dots" role="group" aria-label="Výber slidu">
        <button type="button" class="hslider__dot is-active" data-hslider-go="0" aria-label="Slide 1: VERIGA"></button>
        <button type="button" class="hslider__dot" data-hslider-go="1" aria-label="Slide 2: Predaj LKT"></button>
        <button type="button" class="hslider__dot" data-hslider-go="2" aria-label="Slide 3: Náhradné diely LKT"></button>
    </div>
</div>

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

<section class="section home-veriga">
        <div class="home-veriga__media">
            <img src="/assets/images/veriga/hero-snow.webp" alt="Traktor so snehovými reťazami VERIGA v zime" width="1600" height="900" loading="lazy">
        </div>
    <div class="container home-veriga__grid">
        <div class="home-veriga__text">
            <img src="/assets/images/veriga/veriga-logo.png" alt="VERIGA since 1922" class="home-veriga__logo" width="230" height="53" loading="lazy">
            <span class="section__eyebrow">Spolupracujeme s VERIGA</span>
            <h2>Snehové reťaze VERIGA &ndash; zástupca pre Slovensko</h2>
            <p>Slovinský výrobca VERIGA patrí medzi popredných výrobcov snehových reťazí. My sme jeho zástupca na Slovensku. Reťaze dodávame pre osobné a nákladné autá, traktory, pracovné aj lesné stroje.</p>
            <ul class="home-veriga__list">
                <li>Osobné a nákladné autá</li>
                <li>Pracovné stroje a traktory</li>
                <li>Lesné stroje</li>
            </ul>
            <a href="/snehove-retaze-veriga" data-goto="dopyt" class="btn btn--primary btn--lg">Vyžiadať cenovú ponuku</a>
        </div>
    </div>
</section>
<section class="section section--alt home-paths home-paths--clean">
    <div class="container">
        <div class="section__header sr">
            <span class="section__eyebrow">Ako vám vieme pomôcť</span>
            <h2>Vyberte si ďalší krok</h2>
            <p>Či už hľadáte konkrétny diel, nové vybavenie alebo radu, dostanete sa k nej na pár kliknutí.</p>
        </div>
        <div class="home-paths__grid">
            <a href="/produkty" class="home-path home-path--products">
                <span class="home-path__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </span>
                <span class="home-path__number">01</span>
                <span class="home-path__label">E-shop</span>
                <h3>Prehľadať sortiment</h3>
                <p>Produkty, náhradné diely a príslušenstvo pre lesnú techniku.</p>
                <span class="home-path__cta">Zobraziť produkty <b aria-hidden="true">&rarr;</b></span>
            </a>
            <a href="/kontakt" class="home-path home-path--advice">
                <span class="home-path__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </span>
                <span class="home-path__number">02</span>
                <span class="home-path__label">Poradenstvo</span>
                <h3>Potrebujete pomôcť s výberom?</h3>
                <p>Napíšte nám a pripravíme odporúčanie podľa vašej práce.</p>
                <span class="home-path__cta">Napísať nám <b aria-hidden="true">&rarr;</b></span>
            </a>
            <a href="/o-nas" class="home-path home-path--company">
                <span class="home-path__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg>
                </span>
                <span class="home-path__number">03</span>
                <span class="home-path__label">O spoločnosti</span>
                <h3>Spoznajte naše skúsenosti</h3>
                <p>Prečítajte si viac o ORVEX, našej technike a službách.</p>
                <span class="home-path__cta">O nás <b aria-hidden="true">&rarr;</b></span>
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
                    <a href="<?= e(productUrl($product)) ?>" class="product-card__image">
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
                        <h3><a href="<?= e(productUrl($product)) ?>"><?= e($product['name']) ?></a></h3>
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
                            <a href="<?= e(productUrl($product)) ?>#dopyt" class="btn btn--outline btn--sm">Vyžiadať cenu</a>
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

<section class="cta-banner cta-banner--photo">
    <div class="container cta-banner__grid">
        <div class="cta-banner__content sr">
            <span class="cta-banner__eyebrow">Poradenstvo</span>
            <h2>Potrebujete poradiť s výberom techniky?</h2>
            <p>Naši odborníci vám pomôžu vybrať optimálne riešenie pre vaše potreby. Kontaktujte nás pre nezáväznú konzultáciu.</p>
            <div class="cta-banner__actions">
                <a href="/kontakt" class="btn btn--white btn--lg">Napíšte nám</a>
                <a href="/produkty" class="btn btn--cta-secondary btn--lg">Prehliadať sortiment</a>
            </div>
        </div>
        <aside class="cta-card sr" aria-label="Kontakt">
            <span class="cta-card__label">Zavolajte nám</span>
            <a href="tel:+421905500950" class="cta-card__phone">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                0905 500 950
            </a>
            <ul class="cta-card__list">
                <li>Po &ndash; Pi: 8:00 &ndash; 16:00</li>
                <li><a href="mailto:orvex@orvex.sk">orvex@orvex.sk</a></li>
                <li>Odborné poradenstvo zdarma</li>
            </ul>
        </aside>
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
