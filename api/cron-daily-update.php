<?php
// Nocna aktualizacia dat z MRP - spustat cez CLI cron (napr. 0 2 * * * na hostingu).
// 1) Obnovi produktovu cache (cerstve ceny/sklady/nove produkty).
// 2) Dotiahne fotky pre produkty, ktore este ziadnu nemaju (nove produkty od
//    minulej noci, prip. zvysky po predoslom neuplnom behu).
//
// Spustenie: php api/cron-daily-update.php
// NEVOLAT cez URL/prehliadac - beh moze trvat aj niekolko minut.
//
// Davky su umyselne male a obmedzene poctom (BATCH_SIZE / MAX_BATCHES) -
// niektore shared hostingy zabijaju dlho bezice CLI procesy (u nas cca po
// 3 minutach, aj na pozadi/nohup). Bezny nocny beh (par novych produktov)
// sa do limitu pohodlne zmesti; velky jednorazovy dohlad pri prvom nasadeni
// treba spustit opakovane (viackrat po sebe, viz README/dokumentacia).
const BATCH_SIZE = 25;
const MAX_BATCHES = 3;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/MrpApi.php';

function logLine(string $msg): void
{
    echo '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n";
    @ob_flush();
    @flush();
}

$start = microtime(true);
$api = new MrpApi();

logLine('Obnovujem produktovu cache...');
$api->clearCache();
$products = $api->getProducts();
logLine('Nacitanych produktov: ' . count($products));

$missingIds = array_column(array_filter($products, fn($p) => empty($p['image'])), 'id');
$noImage = array_flip($api->getNoImageIds());
$onlyIds = array_values(array_filter($missingIds, fn($id) => !isset($noImage[$id])));

if (empty($onlyIds)) {
    logLine('Vsetky produkty uz maju fotku (alebo ju MRP pre ne nema). Import fotiek sa preskakuje.');
    $imgStats = ['saved' => 0, 'errors' => 0];
} else {
    logLine('Chybajucich fotiek: ' . count($missingIds) . ', z toho este neoverenych: ' . count($onlyIds) . '. Startujem doplnenie...');
    $imgStats = $api->importAllImages(function (array $stats, ?string $error) {
        $line = "davka {$stats['done']}/{$stats['batches']} - ulozene: {$stats['saved']}, preskocene: {$stats['skipped']}, chyby: {$stats['errors']}";
        if ($error) {
            $line .= ' | ' . $error;
        }
        logLine($line);
    }, BATCH_SIZE, $onlyIds, MAX_BATCHES);
}

// Cesty k obrazkom v cache/products.json sa pocitaju v case parsovania (pred
// importom fotiek vyssie), takze novo stiahnute fotky by sa v cache prejavili
// az pri dalsom obnoveni. Preto tu cache este raz obnovime, aby web hned
// ukazoval aj prave stiahnute fotky.
if (($imgStats['saved'] ?? 0) > 0) {
    logLine('Obnovujem cache este raz, aby sa prejavili novo stiahnute fotky...');
    $api->clearCache();
    $api->getProducts();
}

$elapsed = round(microtime(true) - $start);
logLine("Hotovo za {$elapsed}s. Nove fotky: {$imgStats['saved']}, chyby: {$imgStats['errors']}.");
