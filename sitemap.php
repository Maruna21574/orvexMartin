<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=UTF-8');

function sitemapUrl(string $loc, string $changefreq, string $priority, ?string $lastmod = null): string
{
    $xml = "  <url>\n";
    $xml .= '    <loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
    if ($lastmod !== null) {
        $xml .= '    <lastmod>' . $lastmod . "</lastmod>\n";
    }
    $xml .= '    <changefreq>' . $changefreq . "</changefreq>\n";
    $xml .= '    <priority>' . $priority . "</priority>\n";
    $xml .= "  </url>\n";
    return $xml;
}

$today = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Staticke stranky
echo sitemapUrl(SITE_URL . '/', 'daily', '1.0', $today);
echo sitemapUrl(SITE_URL . '/produkty', 'daily', '0.9', $today);
echo sitemapUrl(SITE_URL . '/snehove-retaze-veriga', 'monthly', '0.8');
require_once __DIR__ . '/includes/veriga.php';
foreach (verigaTypes() as $verigaType) {
    echo sitemapUrl(SITE_URL . '/snehove-retaze-veriga/' . $verigaType['slug'], 'monthly', '0.7');
}
echo sitemapUrl(SITE_URL . '/o-nas', 'monthly', '0.5');
echo sitemapUrl(SITE_URL . '/kontakt', 'monthly', '0.5');
echo sitemapUrl(SITE_URL . '/obchodne-podmienky', 'yearly', '0.2');
echo sitemapUrl(SITE_URL . '/reklamacny-poriadok', 'yearly', '0.2');
echo sitemapUrl(SITE_URL . '/ochrana-osobnych-udajov', 'yearly', '0.2');

// Kategorie a produkty len zo skutocnych MRP dat. getProducts()/getCategories() pri vypadku MRP
// bez cache vracaju vymyslene demo produkty - tie sa do sitemapy dostat nesmu (Google by indexoval
// neexistujuce stranky). Pri vypadku sa preto vynechaju a sitemap ostane s dalsimi strankami platna.
$mrpProducts = [];
$mrpCategories = [];
if (MRP_API_ENABLED) {
    try {
        $mrpProducts = getApi()->getProducts();
        $mrpCategories = getApi()->getCategories();
    } catch (\Throwable $e) {
        $mrpProducts = [];
        $mrpCategories = [];
    }
}

// Kategorie
foreach ($mrpCategories as $category) {
    echo sitemapUrl(SITE_URL . '/produkty?kategoria=' . urlencode($category['id']), 'weekly', '0.7');
}

// Vsetky produkty
foreach ($mrpProducts as $product) {
    echo sitemapUrl(SITE_URL . productUrl($product), 'weekly', '0.8');
}

echo '</urlset>' . "\n";
