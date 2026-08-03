<?php
// URL-spustitelna verzia udrzbovych skriptov (obnova cache / doplnanie fotiek).
//
// Dovod existencie: Websupport CRON typu "Spustenie PHP suboru" pouziva iny
// sietovy vystup nez bezne HTTP poziadavky na web - v praxi to znamena, ze
// spojenie na MRP z toho CRON mechanizmu dostava "Connection refused",
// zatial co ta ista logika spustena cez php-cli v SSH alebo cez normalnu
// HTTP poziadavku na web funguje bez problemov. Tento skript preto sprava
// tu istu funkcnost pod URL, ktoru moze pravidelne volat externa webcron
// sluzba (napr. cron-job.org) alebo si ju clovek moze otvorit rucne.
//
// Pouzitie:
//   /api/cron-run.php?token=...&task=images   - doplni chybajuce fotky (mala davka)
//   /api/cron-run.php?token=...&task=refresh  - obnovi produktovu cache z MRP
//
// Davky pre "images" su umyselne male (default 15 kariet), aby sa cely beh
// zmestil do beznych HTTP/FastCGI timeoutov na zdielanom hostingu (na rozdiel
// od CLI behu, kde je limit radovo minuty, HTTP request casto zabije uz
// 30-60s). Na doplnenie celeho zoznamu chybajucich fotiek treba tento task
// volat opakovane (napr. kazdych 5-10 minut cez externy webcron).

require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');
ignore_user_abort(true);
@set_time_limit(120);

$token = $_GET['token'] ?? '';
if (!defined('CRON_SECRET') || CRON_SECRET === '' || !hash_equals(CRON_SECRET, $token)) {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

require_once __DIR__ . '/MrpApi.php';
$api = new MrpApi();
$task = $_GET['task'] ?? 'images';

function outLine(string $msg): void
{
    echo '[' . date('H:i:s') . '] ' . $msg . "\n";
    @ob_flush();
    @flush();
}

$start = microtime(true);

if ($task === 'refresh') {
    outLine('Obnovujem produktovu cache z MRP...');
    try {
        $products = $api->refreshProducts();
        outLine('OK - nacitanych produktov: ' . count($products));
    } catch (\Throwable $e) {
        outLine('ZLYHALO - ' . get_class($e) . ': ' . $e->getMessage());
    }
} elseif ($task === 'images') {
    $batchSize = max(1, min(30, (int)($_GET['batch'] ?? 15)));
    $maxBatches = max(1, min(3, (int)($_GET['batches'] ?? 1)));

    $products = $api->getProducts();
    $missingIds = array_column(array_filter($products, fn($p) => empty($p['image'])), 'id');
    $noImage = array_flip($api->getNoImageIds());
    $onlyIds = array_values(array_filter($missingIds, fn($id) => !isset($noImage[$id])));

    outLine('Chybajuce fotky: ' . count($missingIds) . ' (zostava overit ' . count($onlyIds) . '), davka=' . $batchSize . ', max davok=' . $maxBatches);

    if (empty($onlyIds)) {
        outLine('Nic na doplnenie.');
    } else {
        $stats = $api->importAllImages(function (array $stats, ?string $error) {
            $line = "davka {$stats['done']}/{$stats['batches']} - ulozene: {$stats['saved']}, preskocene: {$stats['skipped']}, chyby: {$stats['errors']}";
            if ($error) {
                $line .= ' | ' . $error;
            }
            outLine($line);
        }, $batchSize, $onlyIds, $maxBatches);

        if ($stats['saved'] > 0) {
            outLine('Obnovujem cache, aby sa nove fotky hned prejavili...');
            try {
                $api->refreshProducts();
            } catch (\Throwable $e) {
                outLine('MRP nedostupne pri obnove cache (' . $e->getMessage() . '), cache ostava povodna.');
            }
        }
    }
} else {
    http_response_code(400);
    echo "Neznamy task. Pouzi task=images alebo task=refresh.\n";
    exit;
}

$elapsed = round(microtime(true) - $start, 1);
outLine("Hotovo za {$elapsed}s.");
