<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Snehové reťaze VERIGA - zástupca pre Slovensko';
$pageDescription = 'Snehové reťaze VERIGA pre osobné a nákladné autá, pracovné a lesné stroje. ORVEX MT je zástupca VERIGA pre Slovensko. Vyplňte dopyt a zašleme vám cenovú ponuku.';
$hideVerigaPopup = true;
$bodyClass = 'veriga-theme';

$pageJsonLd = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Snehové reťaze VERIGA',
        'description' => $pageDescription,
        'url' => SITE_URL . '/snehove-retaze-veriga',
        'about' => ['@type' => 'Brand', 'name' => 'VERIGA'],
    ],
    [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Domov', 'item' => SITE_URL . '/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Snehové reťaze VERIGA', 'item' => SITE_URL . '/snehove-retaze-veriga'],
        ],
    ],
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/veriga.php';

$veriga_types = verigaTypes();

?>

<section class="veriga-hero" style="background-image:url('/assets/images/veriga/hero-snow.webp')">
    <div class="container veriga-hero__inner">
        <nav class="breadcrumb veriga-hero__crumbs">
            <a href="/">Domov</a>
            <span>/</span>
            <span>Snehové reťaze VERIGA</span>
        </nav>
        <img src="/assets/images/veriga/veriga-logo.png" alt="VERIGA since 1922" class="veriga-hero__logo" width="260" height="60">
        <span class="veriga-hero__badge">Zástupca VERIGA pre Slovensko</span>
        <h1>Snehové reťaze pre autá, pracovné a lesné stroje</h1>
        <p>ORVEX MT je zástupca slovinského výrobcu snehových reťazí <strong>VERIGA</strong> na Slovensku. Povedzte nám, aké pneumatiky a aký stroj máte, a pripravíme vám cenovú ponuku.</p>
        <a href="#dopyt" class="btn btn--primary btn--lg">Vyžiadať cenovú ponuku</a>
    </div>
</section>

<section class="section" id="typy">
    <div class="container">
        <div class="section__header">
            <span class="section__eyebrow">Pre aké stroje</span>
            <h2>Reťaze pre každé vozidlo a stroj</h2>
            <p>Vyberte, na čo reťaze potrebujete. Pri všetkých typoch vám pomôžeme s výberom podľa rozmeru pneumatík a terénu.</p>
        </div>
        <div class="veriga-types">
            <?php foreach ($veriga_types as $key => $vt): ["label" => $label, "image" => $image, "alt" => $alt, "desc" => $desc, "slug" => $slug] = $vt; ?>
                <a href="/snehove-retaze-veriga/<?= e($slug) ?>" class="veriga-type">
                    <img src="/assets/images/veriga/<?= e($image) ?>.webp" alt="<?= e($alt) ?>" width="500" height="300" loading="lazy">
                    <div class="veriga-type__body">
                        <h3><?= e($label) ?></h3>
                        <p><?= e($desc) ?></p>
                        <span class="veriga-type__cta">Zistiť viac &rarr;</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
$modelKeys = ['grip-stud', 'tractor', 'tempo'];
$modelsTitle = 'Reťaze na traktory a pracovné stroje';
$modelsLead = 'Tri základné prevedenia. Výber závisí od zložitosti terénu a hmotnosti stroja.';
require __DIR__ . '/includes/veriga-models.php';
?>

<?php require __DIR__ . '/includes/veriga-form.php'; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
