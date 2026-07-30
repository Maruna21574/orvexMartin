<?php
// Nocna aktualizacia dat z MRP - spustat cez CLI cron (napr. 0 2 * * * na hostingu).
// 1) Obnovi produktovu cache (cerstve ceny/sklady/nove produkty).
// 2) Dotiahne fotky pre produkty, ktore este ziadnu nemaju (nove produkty od
//    minulej noci, prip. zvysky po predoslom neuplnom behu).
//
// Spustenie: php api/cron-daily-update.php
// NEVOLAT cez URL/prehliadac - beh moze trvat desiatky minut (najma import
// fotiek), co presiahne bezne casove limity pre webove requesty.

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

$onlyIds = array_column(array_filter($products, fn($p) => empty($p['image'])), 'id');

if (empty($onlyIds)) {
    logLine('Vsetky produkty uz maju fotku, import fotiek sa preskakuje.');
    $imgStats = ['saved' => 0, 'errors' => 0];
} else {
    logLine('Chybajucich fotiek: ' . count($onlyIds) . '. Startujem doplnenie...');
    $imgStats = $api->importAllImages(function (array $stats, ?string $error) {
        $line = "davka {$stats['done']}/{$stats['batches']} - ulozene: {$stats['saved']}, preskocene: {$stats['skipped']}, chyby: {$stats['errors']}";
        if ($error) {
            $line .= ' | ' . $error;
        }
        logLine($line);
    }, 50, $onlyIds);
}

$elapsed = round(microtime(true) - $start);
logLine("Hotovo za {$elapsed}s. Nove fotky: {$imgStats['saved']}, chyby: {$imgStats['errors']}.");
