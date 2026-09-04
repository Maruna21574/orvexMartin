<?php

require_once ROOT_PATH . '/api/MrpApi.php';

function getApi(): MrpApi
{
    static $api = null;
    if ($api === null) {
        $api = new MrpApi();
    }
    return $api;
}

function formatPrice(float $price): string
{
    return number_format($price, 2, ',', ' ') . ' ' . CURRENCY;
}

function getCart(): array
{
    return $_SESSION['cart'] ?? [];
}

function getCartCount(): int
{
    $cart = getCart();
    $count = 0;
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function getCartTotal(): float
{
    $cart = getCart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function addToCart(string $id, string $name, float $price, int $quantity = 1, string $image = '', string $sku = ''): void
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'image' => $image,
            'sku' => $sku,
        ];
    }
}

function removeFromCart(string $id): void
{
    unset($_SESSION['cart'][$id]);
}

function updateCartQuantity(string $id, int $quantity): void
{
    if (isset($_SESSION['cart'][$id])) {
        if ($quantity <= 0) {
            removeFromCart($id);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $quantity;
        }
    }
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function isActivePage(string $page): string
{
    $current = basename($_SERVER['SCRIPT_NAME'], '.php');
    return $current === $page ? 'active' : '';
}

function getDemoProducts(): array
{
    return [
        [
            'id' => '1',
            'name' => 'Lesný kolesový traktor LKT 81T',
            'description' => 'Výkonný lesný kolesový traktor určený na sústreďovanie dreva v náročných terénnych podmienkach. Robustná konštrukcia, spoľahlivý motor a hydraulický systém zabezpečujú efektívnu prácu v lese.',
            'price' => 45000.00,
            'price_vat' => 54000.00,
            'vat_rate' => 20,
            'sku' => 'LKT-81T',
            'category' => 'Lesná technika',
            'category_id' => 'lesna-technika',
            'stock' => 3,
            'unit' => 'ks',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Motor', 'value' => 'Diesel 81 kW'],
                ['name' => 'Hmotnosť', 'value' => '7 200 kg'],
                ['name' => 'Ťažná sila', 'value' => '80 kN'],
            ],
        ],
        [
            'id' => '2',
            'name' => 'Hydraulické čerpadlo HP-32',
            'description' => 'Vysokotlakové hydraulické čerpadlo s výkonom 32 cm³/ot. Vhodné pre lesné stroje a mobilnú hydrauliku. Dlhá životnosť vďaka kvalitným materiálom.',
            'price' => 890.00,
            'price_vat' => 1068.00,
            'vat_rate' => 20,
            'sku' => 'HP-32',
            'category' => 'Hydraulické čerpadlá',
            'category_id' => 'hydraulicke-cerpadla',
            'stock' => 15,
            'unit' => 'ks',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Výkon', 'value' => '32 cm³/ot'],
                ['name' => 'Tlak', 'value' => '250 bar'],
                ['name' => 'Otáčky', 'value' => '1500-3000 ot/min'],
            ],
        ],
        [
            'id' => '3',
            'name' => 'Snehové reťaze PREMIUM 16.9-30',
            'description' => 'Profesionálne snehové reťaze pre lesné traktory. Špeciálne kalená oceľ odolná voči opotrebeniu. Jednoduché nasadzovanie, výborná trakcia na snehu a ľade.',
            'price' => 420.00,
            'price_vat' => 504.00,
            'vat_rate' => 20,
            'sku' => 'SR-1690-30',
            'category' => 'Snehové reťaze',
            'category_id' => 'snehove-retaze',
            'stock' => 28,
            'unit' => 'pár',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Rozmer', 'value' => '16.9-30'],
                ['name' => 'Materiál', 'value' => 'Kalená oceľ'],
                ['name' => 'Typ', 'value' => 'Rebrový vzor'],
            ],
        ],
        [
            'id' => '4',
            'name' => 'Lanový úväzok 4t / 3m',
            'description' => 'Certifikovaný lanový úväzok s nosnosťou 4 tony a dĺžkou 3 metre. Vhodný na viazanie a manipuláciu s drevom. Splňa normy EN 13414.',
            'price' => 67.50,
            'price_vat' => 81.00,
            'vat_rate' => 20,
            'sku' => 'LU-4T-3M',
            'category' => 'Lanové úväzky',
            'category_id' => 'lanove-uvazky',
            'stock' => 50,
            'unit' => 'ks',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Nosnosť', 'value' => '4 000 kg'],
                ['name' => 'Dĺžka', 'value' => '3 m'],
                ['name' => 'Norma', 'value' => 'EN 13414'],
            ],
        ],
        [
            'id' => '5',
            'name' => 'Náhradný diel - Filter hydrauliky FH-200',
            'description' => 'Originálny filter hydraulického oleja pre lesné stroje. Zabezpečuje čistotu hydraulického systému a predlžuje životnosť komponentov.',
            'price' => 34.90,
            'price_vat' => 41.88,
            'vat_rate' => 20,
            'sku' => 'FH-200',
            'category' => 'Náhradné diely',
            'category_id' => 'nahradne-diely',
            'stock' => 120,
            'unit' => 'ks',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Filtrácia', 'value' => '10 μm'],
                ['name' => 'Prietok', 'value' => '200 l/min'],
                ['name' => 'Pripojenie', 'value' => 'SAE 16'],
            ],
        ],
        [
            'id' => '6',
            'name' => 'Procesová hlavica H270',
            'description' => 'Harvesterová procesová hlavica pre spracovanie dreva priamo v poraste. Automatické meranie dĺžky a priemeru, odvetvovanie a krátenie.',
            'price' => 38500.00,
            'price_vat' => 46200.00,
            'vat_rate' => 20,
            'sku' => 'PH-H270',
            'category' => 'Lesná technika',
            'category_id' => 'lesna-technika',
            'stock' => 1,
            'unit' => 'ks',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Max. priemer', 'value' => '650 mm'],
                ['name' => 'Hmotnosť', 'value' => '1 050 kg'],
                ['name' => 'Podávacia sila', 'value' => '25 kN'],
            ],
        ],
        [
            'id' => '7',
            'name' => 'Oceľové lano 12mm / 50m',
            'description' => 'Vysokopevnostné oceľové lano pre navijaky lesných strojov. Konštrukcia 6x19+FC, pozinkované, odolné voči korózii.',
            'price' => 245.00,
            'price_vat' => 294.00,
            'vat_rate' => 20,
            'sku' => 'OL-12-50',
            'category' => 'Lanové úväzky',
            'category_id' => 'lanove-uvazky',
            'stock' => 18,
            'unit' => 'ks',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Priemer', 'value' => '12 mm'],
                ['name' => 'Dĺžka', 'value' => '50 m'],
                ['name' => 'Únosnosť', 'value' => '10 200 kg'],
            ],
        ],
        [
            'id' => '8',
            'name' => 'Hydraulický valec HV-80/50-400',
            'description' => 'Dvojčinný hydraulický valec pre lesné stroje. Priemer piestu 80 mm, priemer piestnice 50 mm, zdvih 400 mm. Vysoká kvalita tesnení.',
            'price' => 560.00,
            'price_vat' => 672.00,
            'vat_rate' => 20,
            'sku' => 'HV-805040',
            'category' => 'Hydraulické čerpadlá',
            'category_id' => 'hydraulicke-cerpadla',
            'stock' => 8,
            'unit' => 'ks',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Priemer piestu', 'value' => '80 mm'],
                ['name' => 'Zdvih', 'value' => '400 mm'],
                ['name' => 'Tlak', 'value' => '200 bar'],
            ],
        ],
        [
            'id' => '9',
            'name' => 'Snehové reťaze FORESTRY 18.4-34',
            'description' => 'Ťažké snehové reťaze pre veľké lesné traktory. Diamantový vzor pre maximálnu trakciu. Zosilnené články pre extrémne nasadenie.',
            'price' => 580.00,
            'price_vat' => 696.00,
            'vat_rate' => 20,
            'sku' => 'SR-1840-34',
            'category' => 'Snehové reťaze',
            'category_id' => 'snehove-retaze',
            'stock' => 12,
            'unit' => 'pár',
            'image' => '',
            'images' => [],
            'params' => [
                ['name' => 'Rozmer', 'value' => '18.4-34'],
                ['name' => 'Vzor', 'value' => 'Diamantový'],
                ['name' => 'Hmotnosť', 'value' => '95 kg/pár'],
            ],
        ],
    ];
}

function getDemoCategories(): array
{
    return [
        ['id' => 'lesna-technika', 'name' => 'Lesná technika', 'parent_id' => '', 'count' => 2, 'image' => ''],
        ['id' => 'hydraulicke-cerpadla', 'name' => 'Hydraulické čerpadlá', 'parent_id' => '', 'count' => 2, 'image' => ''],
        ['id' => 'snehove-retaze', 'name' => 'Snehové reťaze', 'parent_id' => '', 'count' => 2, 'image' => ''],
        ['id' => 'lanove-uvazky', 'name' => 'Lanové úväzky', 'parent_id' => '', 'count' => 2, 'image' => ''],
        ['id' => 'nahradne-diely', 'name' => 'Náhradné diely', 'parent_id' => '', 'count' => 1, 'image' => ''],
    ];
}

function getProducts(array $filters = []): array
{
    if (!MRP_API_ENABLED) {
        return filterDemoProducts($filters);
    }
    try {
        return getApi()->getProducts($filters);
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log('MRP API Error: ' . $e->getMessage());
        }
        return filterDemoProducts($filters);
    }
}

function getProduct(string $id): ?array
{
    if (!MRP_API_ENABLED) {
        foreach (getDemoProducts() as $p) {
            if ($p['id'] === $id) return $p;
        }
        return null;
    }
    try {
        return getApi()->getProduct($id);
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log('MRP API Error: ' . $e->getMessage());
        }
        foreach (getDemoProducts() as $p) {
            if ($p['id'] === $id) return $p;
        }
        return null;
    }
}

function getCategories(): array
{
    if (!MRP_API_ENABLED) {
        return getDemoCategories();
    }
    try {
        return getApi()->getCategories();
    } catch (Exception $e) {
        return getDemoCategories();
    }
}

function filterDemoProducts(array $filters): array
{
    $products = getDemoProducts();

    if (!empty($filters['search'])) {
        $search = mb_strtolower($filters['search']);
        $products = array_filter($products, function ($p) use ($search) {
            return str_contains(mb_strtolower($p['name']), $search)
                || str_contains(mb_strtolower($p['description']), $search)
                || str_contains(mb_strtolower($p['sku']), $search);
        });
    }

    if (!empty($filters['category'])) {
        $cat = $filters['category'];
        $products = array_filter($products, fn($p) => $p['category_id'] === $cat);
    }

    return array_values($products);
}

function mb_stricmp(string $a, string $b): int
{
    return strcmp(mb_strtolower($a, 'UTF-8'), mb_strtolower($b, 'UTF-8'));
}

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Jednoduchá ochrana verejných formulárov (kontakt, dopyt, objednávka) pred
// spamovacimi botmi - bez CAPTCHA/externych sluzieb. Kombinuje honeypot pole
// (skryte cez CSS, nie type="hidden" - to boti castejsie preskocia) s
// kontrolou minimalneho casu medzi vykreslenim a odoslanim formulara (boti
// zvycajne odosielaju takmer okamzite).
function renderAntiSpamFields(): string
{
    return '<div class="hp-field" aria-hidden="true"><label for="extra_info">Nechajte prázdne</label>'
        . '<input type="text" id="extra_info" name="extra_info" tabindex="-1" autocomplete="off"></div>'
        . '<input type="hidden" name="form_ts" value="' . time() . '">';
}

function isSpamSubmission(): bool
{
    if (trim($_POST['extra_info'] ?? '') !== '') {
        return true;
    }

    $formTs = (int) ($_POST['form_ts'] ?? 0);
    if ($formTs <= 0 || time() - $formTs < 2) {
        return true;
    }

    return false;
}
