<?php
// Docasny diagnosticky skript na zistenie, preco hosting nevie dosiahnut MRP
// API (napr. "MRP nedostupne" v cron-import-images.php / cron-daily-update.php).
// Da sa otvorit priamo v prehliadaci aj spustit ako Websupport naplanovana
// uloha typu "Navsteva URL adresy" (?token=CRON_SECRET) - takto sa da priamo
// porovnat, ci k rozdielu v pripojeni naozaj dochadza medzi beznou HTTP
// navstevou a CRON dispecerom. Po vyrieseni problemu tento subor zmazat a
// zaznam preň odstranit z .htaccess.

require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

$token = $_GET['token'] ?? '';
if (!defined('CRON_SECRET') || CRON_SECRET === '' || !hash_equals(CRON_SECRET, $token)) {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

$host = parse_url(MRP_API_URL, PHP_URL_HOST);
$port = parse_url(MRP_API_URL, PHP_URL_PORT) ?: 80;

echo "=== MRP diagnostika ===\n";
echo 'Cas: ' . date('Y-m-d H:i:s') . "\n";
echo 'MRP_API_URL: ' . MRP_API_URL . " (host={$host}, port={$port})\n\n";

echo "--- 1) Aka je odchadzajuca IP tohto serveru ---\n";
$ipCtx = stream_context_create(['http' => ['timeout' => 5]]);
$myIp = @file_get_contents('https://api.ipify.org', false, $ipCtx);
echo 'Verejna IP: ' . ($myIp !== false ? $myIp : '(nepodarilo sa zistit)') . "\n\n";

echo "--- 2) DNS/rezolucia hosta '{$host}' ---\n";
if (filter_var($host, FILTER_VALIDATE_IP)) {
    echo "Host je uz IP adresa, DNS sa nerobi.\n\n";
} else {
    $resolved = gethostbyname($host);
    echo "Vysledok: {$resolved}" . ($resolved === $host ? ' (DNS zlyhalo)' : '') . "\n\n";
}

echo "--- 3) Holy TCP connect na {$host}:{$port} (fsockopen, timeout 8s) ---\n";
$start = microtime(true);
$errno = 0;
$errstr = '';
$fp = @fsockopen($host, (int)$port, $errno, $errstr, 8);
$elapsed = round(microtime(true) - $start, 2);
if ($fp) {
    echo "OK - spojenie sa podarilo nadviazat za {$elapsed}s.\n";
    fclose($fp);
} else {
    echo "ZLYHALO za {$elapsed}s - errno={$errno}, errstr=\"{$errstr}\"\n";
}
echo "\n";

echo "--- 4) HTTP POST cez cURL priamo na MRP endpoint (timeout 15s) ---\n";
$ch = curl_init(MRP_API_URL);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => '<ping/>',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/xml; charset=windows-1250'],
]);
$start = microtime(true);
$response = curl_exec($ch);
$elapsed = round(microtime(true) - $start, 2);
$curlErrno = curl_errno($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo "cURL ZLYHALO za {$elapsed}s - errno={$curlErrno}, error=\"{$curlError}\"\n";
} else {
    echo "cURL OK za {$elapsed}s - HTTP kod: {$httpCode}, dlzka odpovede: " . strlen($response) . " bajtov\n";
    echo 'Prvych 200 znakov odpovede: ' . substr($response, 0, 200) . "\n";
}
echo "\n";

echo "--- 5) Plny pokus cez MrpApi::refreshProducts() ---\n";
require_once __DIR__ . '/MrpApi.php';
$api = new MrpApi();
$start = microtime(true);
try {
    $products = $api->refreshProducts();
    $elapsed = round(microtime(true) - $start, 2);
    echo 'OK za ' . $elapsed . 's - nacitanych produktov: ' . count($products) . "\n";
} catch (\Throwable $e) {
    $elapsed = round(microtime(true) - $start, 2);
    echo 'ZLYHALO za ' . $elapsed . 's - ' . get_class($e) . ': ' . $e->getMessage() . "\n";
}

echo "\n=== Koniec diagnostiky ===\n";
