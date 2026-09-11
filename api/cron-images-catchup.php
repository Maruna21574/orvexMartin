<?php
// Docasny "dobiehaci" cron na rychlejsie doplnenie historickeho dlhu
// chybajucich fotiek - spusta sa castejsie nez standardny cron-daily-update.php
// (napr. kazdych 15-20 min cez crontab). Narozdiel od neho NEROBI plny
// refresh cien/skladu na zaciatku kazdeho behu (to staci raz denne cez
// cron-daily-update.php) - len dopĺňa chybajúce fotky z poslednej platnej
// cache, co je lacna operacia (ziadne dodatocne zataziny MRP server cenami).
//
// Po dobehnuti dlhu (0 chybajucich fotiek v logu) tento riadok z crontabu
// odstranit - nocny cron-daily-update.php uz sam priebezne doplna fotky
// k novo pridanym produktom.
//
// Spustenie: php api/cron-images-catchup.php
// 25 kariet moze tvorit vyse 50 MB a prekrocit 120s timeout MRP.
// Mensie davky ukladaju priebeh castejsie aj pri velkych fotkach.
const BATCH_SIZE = 5;
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

$products = $api->getProducts();
$missingIds = array_column(array_filter($products, fn($p) => empty($p['image'])), 'id');
$noImage = array_flip($api->getNoImageIds());
$onlyIds = array_values(array_filter($missingIds, fn($id) => !isset($noImage[$id])));

logLine('Chybajucich fotiek: ' . count($missingIds) . ', na doplnenie: ' . count($onlyIds));
logLine('Prve karty na overenie: ' . implode(', ', array_slice($onlyIds, 0, 10)));
logLine('Velkost davky: ' . BATCH_SIZE . ', maximum davok: ' . MAX_BATCHES);

if (empty($onlyIds)) {
    logLine('Vsetko doplnene, netreba nic stahovat.');
} else {
    $imgStats = $api->importAllImages(function (array $stats, ?string $error) {
        $line = "davka {$stats['done']}/{$stats['batches']} - ulozene: {$stats['saved']}, preskocene: {$stats['skipped']}, chyby: {$stats['errors']}";
        $line .= " | existujuce fotky: {$stats['existing']}, bez fotky v MRP: {$stats['no_image']}";
        if ($error) {
            $line .= ' | ' . $error;
        }
        logLine($line);
    }, BATCH_SIZE, $onlyIds, MAX_BATCHES);

    logLine('Priebeh sa uklada po kazdej uspesnej davke priamo do cache.');
}

$elapsed = round(microtime(true) - $start);
logLine("Hotovo za {$elapsed}s.");
