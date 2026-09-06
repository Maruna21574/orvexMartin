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
echo sitemapUrl(SITE_URL . '/o-nas', 'monthly', '0.5');
echo sitemapUrl(SITE_URL . '/kontakt', 'monthly', '0.5');
echo sitemapUrl(SITE_URL . '/obchodne-podmienky', 'yearly', '0.2');
echo sitemapUrl(SITE_URL . '/reklamacny-poriadok', 'yearly', '0.2');
echo sitemapUrl(SITE_URL . '/ochrana-osobnych-udajov', 'yearly', '0.2');

// Kategorie
foreach (getCategories() as $category) {
    echo sitemapUrl(SITE_URL . '/produkty?kategoria=' . urlencode($category['id']), 'weekly', '0.7');
}

// Vsetky produkty
foreach (getProducts() as $product) {
    echo sitemapUrl(SITE_URL . '/produkt?id=' . urlencode($product['id']), 'weekly', '0.8');
}

echo '</urlset>' . "\n";
