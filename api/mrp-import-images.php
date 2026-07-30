<?php
// Jednorazovy/prilezitostny skript na hromadne stiahnutie produktovych fotiek z MRP
// a ich konverziu do WebP. Spustit z prikazoveho riadku: php api/mrp-import-images.php
// Volitelne: php api/mrp-import-images.php --only-missing --max-batches=8
// (MRP po ~30-40 min nepretrzitych requestov zacne padat na timeout/HTTP 500,
// preto sa oplati bezat po kratsich useckoch namiesto jedneho dlheho behu).

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/MrpApi.php';

$onlyMissing = in_array('--only-missing', $argv, true);
$maxBatches = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--max-batches=')) {
        $maxBatches = (int) substr($arg, strlen('--max-batches='));
    }
}

$api = new MrpApi();

$onlyIds = null;
if ($onlyMissing) {
    // Cache sa musi obnovit, inak by "chybajuce" vzdy zahrnalo aj uz davno
    // stiahnute fotky z predchadzajuceho (mozno prerusenehho) behu - opakovane
    // spustanie by tak nikdy nepostupilo dalej.
    $api->clearCache();
    $missingIds = array_column(array_filter($api->getProducts(), fn($p) => empty($p['image'])), 'id');

    // Karty, o ktorych uz vieme, ze v MRP nemaju fotku, vynechame - inak by
    // kazdy beh cast rozpoctu davok mrhal na ich opatovne overovanie.
    $noImage = array_flip($api->getNoImageIds());
    $onlyIds = array_values(array_filter($missingIds, fn($id) => !isset($noImage[$id])));

    $skippedKnown = count($missingIds) - count($onlyIds);
    echo "Chybajuce fotky: " . count($missingIds) . " ({$skippedKnown} uz overenych bez fotky, zostava overit " . count($onlyIds) . ").\n";
}

echo "Startujem import obrazkov...\n";
$start = microtime(true);

$stats = $api->importAllImages(function (array $stats, ?string $error) use ($start) {
    $elapsed = round(microtime(true) - $start);
    $line = sprintf(
        '[%ds] davka %d/%d - ulozene: %d, preskocene: %d, chyby: %d',
        $elapsed,
        $stats['done'],
        $stats['batches'],
        $stats['saved'],
        $stats['skipped'],
        $stats['errors']
    );
    if ($error) {
        $line .= ' | ' . $error;
    }
    echo $line . "\n";
    @ob_flush();
    @flush();
}, 50, $onlyIds, $maxBatches);

$elapsed = round(microtime(true) - $start);
echo "\nHotovo za {$elapsed}s.\n";
echo "Davky: {$stats['batches']}, ulozene nove obrazky: {$stats['saved']}, preskocene (uz existovali/bez obrazku): {$stats['skipped']}, chyby: {$stats['errors']}\n";
