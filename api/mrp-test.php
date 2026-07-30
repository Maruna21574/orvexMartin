<?php
require_once __DIR__ . '/../config.php';

$secretKey = base64_decode(MRP_AES_KEY, true);

$key1 = hash_hmac('sha256', chr(1), $secretKey, true);
$key2 = hash_hmac('sha256', $key1 . chr(2), $secretKey, true);

$encKeyBase = $key1;
$authKey = $key2;

function decryptMrpResponse(string $response, string $encKeyBase, string $authKey): string
{
    $xml = simplexml_load_string($response);
    if (!$xml || !isset($xml->encodedBody)) {
        return $response;
    }

    $encodedBody = $xml->encodedBody;

    $paramsBytes = base64_decode((string)$encodedBody->encodingParams, true);
    $encryptedData = base64_decode((string)$encodedBody->encodedData, true);
    $authCode = base64_decode((string)$encodedBody->authCode, true);

    $expectedAuth = hash_hmac('sha256', $paramsBytes . $encryptedData, $authKey, true);

    if (!hash_equals($expectedAuth, $authCode)) {
        throw new RuntimeException('Neplatný HMAC v odpovedi');
    }

    $paramsXml = simplexml_load_string($paramsBytes);
    $varKey = base64_decode((string)$paramsXml->varKey, true);

    $finalEncKey = hash_hmac('sha256', $varKey, $encKeyBase, true);
    $iv = substr(hash('sha256', $varKey, true), 0, 16);

    $plain = openssl_decrypt(
        $encryptedData,
        'aes-256-ctr',
        $finalEncKey,
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($plain === false) {
        throw new RuntimeException('Dešifrovanie odpovede zlyhalo');
    }

    return iconv('Windows-1250', 'UTF-8//IGNORE', $plain);
}

$requestXml = '<?xml version="1.0" encoding="windows-1250"?>
<mrpRequest>
  <request command="EXPEO0" requestId="1"/>
  <data>
    <filter>
      <fltvalue name="stavy">F</fltvalue>
      <fltvalue name="malObraz">F</fltvalue>
      <fltvalue name="velObraz">F</fltvalue>
      <fltvalue name="ciskat">1</fltvalue>
    </filter>
    <paging>
      <RowFrom>1</RowFrom>
      <RowTo>10</RowTo>
    </paging>
  </data>
</mrpRequest>';

$requestBytes = iconv('UTF-8', 'Windows-1250//TRANSLIT', $requestXml);

$varKey = random_bytes(32);
$finalEncKey = hash_hmac('sha256', $varKey, $encKeyBase, true);
$iv = substr(hash('sha256', $varKey, true), 0, 16);

$encryptedData = openssl_encrypt(
    $requestBytes,
    'aes-256-ctr',
    $finalEncKey,
    OPENSSL_RAW_DATA,
    $iv
);

$paramsXml = '<mrpEncodingParams encryption="aes"><varKey>' . base64_encode($varKey) . '</varKey></mrpEncodingParams>';
$paramsBytes = iconv('UTF-8', 'Windows-1250//TRANSLIT', $paramsXml);

$authCode = hash_hmac('sha256', $paramsBytes . $encryptedData, $authKey, true);

$postXml = '<?xml version="1.0" encoding="windows-1250"?>
<mrpEnvelope>
  <encodedBody authentication="hmac_sha256">
    <encodingParams><![CDATA[' . base64_encode($paramsBytes) . ']]></encodingParams>
    <encodedData><![CDATA[' . base64_encode($encryptedData) . ']]></encodedData>
    <authCode><![CDATA[' . base64_encode($authCode) . ']]></authCode>
  </encodedBody>
</mrpEnvelope>';

$ch = curl_init(MRP_API_URL);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => iconv('UTF-8', 'Windows-1250//TRANSLIT', $postXml),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => false,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/xml; charset=windows-1250',
    ],
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);

echo "HTTP CODE: " . ($info['http_code'] ?? 'N/A') . PHP_EOL;

if ($error) {
    die("CURL ERROR: $error" . PHP_EOL);
}

$plainXml = decryptMrpResponse($response, $encKeyBase, $authKey);

file_put_contents(__DIR__ . '/../mrp-products.xml', $plainXml);

echo "Hotovo. Čisté XML uložené do mrp-products.xml" . PHP_EOL;
echo substr($plainXml, 0, 1000) . PHP_EOL;
