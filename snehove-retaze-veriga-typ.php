<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/veriga.php';

// Podstranka jedneho typu snehovych retazi VERIGA (data v includes/veriga.php).
$found = verigaTypeBySlug((string) ($_GET['slug'] ?? ''));
if ($found === null) {
    require __DIR__ . '/404.php';
    exit;
}
[$typeKey, $vt] = $found;

$pageTitle = $vt['title'];
$pageDescription = $vt['metaDesc'];
$hideVerigaPopup = true;
$bodyClass = 'veriga-theme';
$veriga_preselect = $typeKey;

$pageUrl = SITE_URL . '/snehove-retaze-veriga/' . $vt['slug'];
$pageImage = SITE_URL . '/assets/images/veriga/' . $vt['image'] . '.webp';

$details = verigaDetails()[$typeKey] ?? [];

$pageJsonLd = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Domov', 'item' => SITE_URL . '/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Snehové reťaze VERIGA', 'item' => SITE_URL . '/snehove-retaze-veriga'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $vt['label'], 'item' => $pageUrl],
        ],
    ]
];

$pageCanonical = $pageUrl;
require_once __DIR__ . '/includes/header.php';

$otherTypes = array_filter(verigaTypes(), static fn(string $k): bool => $k !== $typeKey, ARRAY_FILTER_USE_KEY);
?>

<section class="vtype-hero">
    <div class="container vtype-hero__grid">
        <div class="vtype-hero__text">
            <nav class="breadcrumb">
                <a href="/">Domov</a>
                <span>/</span>
                <a href="/snehove-retaze-veriga">Snehové reťaze VERIGA</a>
                <span>/</span>
                <span><?= e($vt['label']) ?></span>
            </nav>
            <img src="/assets/images/veriga/veriga-logo.png" alt="VERIGA since 1922" class="vtype-hero__logo" width="180" height="42">
            <h1><?= e($vt['h1']) ?></h1>
            <p><?= e($vt['lead']) ?></p>
            <a href="#dopyt" class="btn btn--primary btn--lg">Vyžiadať cenovú ponuku</a>
        </div>
        <div class="vtype-hero__media">
            <img src="/assets/images/veriga/<?= e($vt['image']) ?>.webp" alt="<?= e($vt['alt']) ?>" width="500" height="300">
        </div>
    </div>
</section>

<?php
$imgBase = '/assets/images/veriga/typy/';
$hasLines = !empty($details['lines']);
?>

<?php if ($hasLines): ?>
    <section class="section section--alt vtype-lines">
        <div class="container">
            <div class="section__header">
                <span class="section__eyebrow">Rady a modely</span>
                <h2>Rady reťazí VERIGA</h2>
                <p>Model vyberieme podľa vášho vozidla, pneumatík a spôsobu použitia.</p>
            </div>
            <?php foreach ($details['lines'] as $i => [$lineName, $lineText, $linePhoto, $lineAlt, $lineModels]): ?>
                <article class="vline<?= $i % 2 ? ' vline--reverse' : '' ?>">
                    <figure class="vline__photo">
                        <img src="<?= e($imgBase . $linePhoto) ?>.webp" alt="<?= e($lineAlt) ?>" loading="lazy">
                    </figure>
                    <div class="vline__body">
                        <h3><?= e($lineName) ?></h3>
                        <p><?= e($lineText) ?></p>
                        <div class="vmodel-row">
                            <?php foreach ($lineModels as [$modelFile, $modelName]): ?>
                                <figure class="vmodel-item">
                                    <img src="<?= e($imgBase . $modelFile) ?>.webp" alt="Snehová reťaz VERIGA <?= e($modelName) ?>" loading="lazy">
                                    <figcaption><?= e($modelName) ?></figcaption>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php elseif (!empty($details['renders'])): ?>
    <section class="section section--alt vtype-lines">
        <div class="container">
            <div class="section__header">
                <span class="section__eyebrow">Ukážky</span>
                <h2>Ukážky reťazí VERIGA</h2>
                <p>Výber modelov. Konkrétny model a rozmer vyberieme podľa vášho vozidla.</p>
            </div>
            <div class="vmodel-gallery vmodel-gallery--<?= count($details['renders']) ?>">
                <?php foreach ($details['renders'] as [$modelFile, $modelName]): ?>
                    <figure class="vmodel-item">
                        <img src="<?= e($imgBase . $modelFile) ?>.webp" alt="Snehová reťaz VERIGA, <?= e(mb_strtolower($vt['label'])) ?>" loading="lazy">
                        <?php if ($modelName !== ''): ?><figcaption><?= e($modelName) ?></figcaption><?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<section class="section">
    <div class="container vtype-body">
        <div>
            <h2>Ako vybrať správne reťaze</h2>
            <ul class="vtype-tips">
                <?php foreach ($vt['tips'] as $tip): ?>
                    <li><?= e($tip) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <aside class="vtype-need">
            <h3>Čo nám napíšte</h3>
            <ol>
                <li>Označenie pneumatík</li>
                <li>Označenie stroja alebo vozidla</li>
                <li>Počet párov</li>
            </ol>
            <p>Cenovú ponuku vám zašleme e-mailom.</p>
        </aside>
    </div>
</section>

<?php if (!empty($vt['models'])): ?>
    <?php
    $modelKeys = $vt['models'];
    $isForest = $typeKey === 'lesne';
    $modelsTitle = $isForest ? 'Reťaze VERIGA pre lesné stroje' : 'Reťaze VERIGA pre traktory a pracovné stroje';
    $modelsLead = $isForest
        ? 'GRIP STUD je hrotová reťaz určená priamo pre lesníkov. TRACTOR a TEMPO sa hodia aj do lesných porastov. Výber závisí od terénu a hmotnosti stroja.'
        : 'Výber závisí od zložitosti terénu a hmotnosti stroja.';
    require __DIR__ . '/includes/veriga-models.php';
    ?>
<?php endif; ?>

<section class="section vtype-benefits">
    <div class="container">
        <div class="vbenefits">
            <div class="vbenefit">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z"/><polyline points="9 12 11 14 15 10"/></svg>
                <strong>Zástupca VERIGA pre Slovensko</strong>
                <span>Reťaze priamo od zástupcu výrobcu.</span>
            </div>
            <div class="vbenefit">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <strong>Pomoc s výberom</strong>
                <span>Poradíme podľa pneumatík, stroja a terénu.</span>
            </div>
            <div class="vbenefit">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                <strong>Cenová ponuka na mieru</strong>
                <span>Pošleme vám ju e-mailom.</span>
            </div>
            <div class="vbenefit">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
                <strong>Aj iné rozmery</strong>
                <span>Všetky ostatné rozmery na vyžiadanie.</span>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/veriga-form.php'; ?>

<section class="section vtype-more">
    <div class="container">
        <div class="vtype-more__head">
            <div>
                <span class="section__eyebrow">Pre iné vozidlá a stroje</span>
                <h2>Ďalšie typy reťazí VERIGA</h2>
            </div>
            <a href="/snehove-retaze-veriga" class="vtype-more__all">Všetky typy &rarr;</a>
        </div>
        <div class="vtype-others">
            <?php foreach ($otherTypes as $other): ?>
                <a href="/snehove-retaze-veriga/<?= e($other['slug']) ?>" class="vtype-other">
                    <img src="/assets/images/veriga/<?= e($other['image']) ?>.webp" alt="<?= e($other['alt']) ?>" width="500" height="300" loading="lazy">
                    <span class="vtype-other__label">
                        <strong><?= e($other['label']) ?></strong>
                        <em>Zistiť viac &rarr;</em>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
