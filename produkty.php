<?php
$pageTitle = 'Produkty';
require_once 'includes/header.php';

$search = trim($_GET['hladaj'] ?? '');
$categoryFilter = trim($_GET['kategoria'] ?? '');
$sort = trim($_GET['zoradit'] ?? '');

$filters = [];
if ($search !== '') {
    $filters['search'] = $search;
}
if ($categoryFilter !== '') {
    $filters['category'] = $categoryFilter;
}

$products = getProducts($filters);

$sortOptions = [
    '' => 'Predvolené',
    'name-asc' => 'Názov A-Z',
    'name-desc' => 'Názov Z-A',
    'price-asc' => 'Cena od najnižšej',
    'price-desc' => 'Cena od najvyššej',
    'stock-desc' => 'Skladom prvé',
];

if ($sort !== '') {
    usort($products, function ($a, $b) use ($sort) {
        switch ($sort) {
            case 'name-asc': return mb_stricmp($a['name'], $b['name']);
            case 'name-desc': return mb_stricmp($b['name'], $a['name']);
            case 'price-asc': return $a['price_vat'] <=> $b['price_vat'];
            case 'price-desc': return $b['price_vat'] <=> $a['price_vat'];
            case 'stock-desc': return $b['stock'] <=> $a['stock'];
            default: return 0;
        }
    });
}
$categories = getCategories();

$activeCategoryName = '';
if ($categoryFilter) {
    foreach ($categories as $cat) {
        if ($cat['id'] === $categoryFilter) {
            $activeCategoryName = $cat['name'];
            break;
        }
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1>
            <?php if ($activeCategoryName): ?>
                <?= e($activeCategoryName) ?>
            <?php elseif ($search): ?>
                Výsledky pre: "<?= e($search) ?>"
            <?php else: ?>
                Všetky produkty
            <?php endif; ?>
        </h1>
        <nav class="breadcrumb">
            <a href="/">Domov</a>
            <span>/</span>
            <span>Produkty</span>
            <?php if ($activeCategoryName): ?>
                <span>/</span>
                <span><?= e($activeCategoryName) ?></span>
            <?php endif; ?>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="products-layout">
            <aside class="products-sidebar" id="filterSidebar">
                <div class="sidebar-overlay" id="sidebarOverlay"></div>
                <div class="sidebar-content">
                    <div class="sidebar-header">
                        <h3>Filtre</h3>
                        <button class="sidebar-close" id="sidebarClose" aria-label="Zavrieť filtre">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <form action="/produkty" method="GET" class="search-form" id="searchForm">
                        <div class="search-input">
                            <input type="text" name="hladaj" placeholder="Hľadať produkty..." value="<?= e($search) ?>">
                            <button type="submit" aria-label="Hľadať">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </button>
                        </div>
                    </form>

                    <div class="filter-group">
                        <h4>Kategórie</h4>
                        <ul class="filter-list">
                            <li>
                                <a href="/produkty" class="<?= $categoryFilter === '' ? 'active' : '' ?>">
                                    Všetky kategórie
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="/produkty?kategoria=<?= e($cat['id']) ?>"
                                       class="<?= $categoryFilter === $cat['id'] ? 'active' : '' ?>">
                                        <?= e($cat['name']) ?>
                                        <span class="filter-count"><?= $cat['count'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </aside>

            <div class="products-main">
                <div class="products-toolbar">
                    <div class="products-toolbar__left">
                        <button class="btn btn--outline btn--sm btn--filter" id="filterToggle">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/></svg>
                            Filtre
                        </button>
                        <span class="products-count"><?= count($products) ?> produktov</span>
                    </div>
                    <div class="products-sort">
                        <label for="sortSelect">Zoradiť:</label>
                        <select id="sortSelect" onchange="applySort(this.value)">
                            <?php foreach ($sortOptions as $val => $label): ?>
                                <option value="<?= e($val) ?>" <?= $sort === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <h3>Žiadne produkty</h3>
                        <p>Pre zadané kritériá sme nenašli žiadne produkty.</p>
                        <a href="/produkty" class="btn btn--primary">Zobraziť všetky produkty</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
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
                                            <span class="product-card__price-vat">Cena na vyžiadanie</span>
                                        </div>
                                        <a href="/produkt?id=<?= e($product['id']) ?>#dopyt" class="btn btn--outline btn--sm">Vyžiadať cenu</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
