<?php
// Offline regression: php tests/mrp-image-progress.php
define('ROOT_PATH', sys_get_temp_dir() . '/orvex-mrp-test-' . bin2hex(random_bytes(6)));
require __DIR__ . '/../api/MrpApi.php';

class FixtureMrpApi extends MrpApi
{
    public string $response = '';
    public function __construct() {}
    protected function sendCommand(string $command, array $filterOverrides = [], int $timeoutSeconds = 30): SimpleXMLElement
    {
        return new SimpleXMLElement($this->response);
    }
}

function check(bool $ok, string $message): void
{
    if (!$ok) {
        throw new RuntimeException($message);
    }
}

function response(string $rows): string
{
    return '<mrpResponse><data><datasets><karty><rows>' . $rows . '</rows></karty></datasets></data></mrpResponse>';
}

mkdir(ROOT_PATH . '/cache', 0755, true);
mkdir(ROOT_PATH . '/assets/img/produkty', 0755, true);
try {
    file_put_contents(ROOT_PATH . '/cache/products.json', json_encode([
        ['id' => '1', 'image' => '', 'images' => []],
        ['id' => '2', 'image' => '', 'images' => []],
        ['id' => '3', 'image' => '', 'images' => []],
    ]));
    // Only the WebP variant exists; MRP supplies the original filename.
    file_put_contents(ROOT_PATH . '/assets/img/produkty/one.webp', 'fixture');
    $api = new FixtureMrpApi();
    $api->response = response('<row><fields><cislo>1</cislo><velobr>one.jpg</velobr></fields></row><row><fields><cislo>2</cislo></fields></row>');
    $stats = $api->importAllImages(null, 2, ['1', '2', '3'], 1);
    check($stats['existing'] === 1 && $stats['no_image'] === 1 && $stats['errors'] === 0, 'Existing and absent images must be distinguished.');
    // New instance simulates the following cron process.
    $next = new FixtureMrpApi();
    $missing = array_column(array_filter($next->getProducts(), fn($p) => empty($p['image'])), 'id');
    $remaining = array_values(array_diff($missing, $next->getNoImageIds()));
    check($remaining === ['3'], 'Next run must advance past both processed products.');
    $next->response = response('<row><fields><cislo>999</cislo></fields></row>');
    $stats = $next->importAllImages(null, 1, ['3'], 1);
    check($stats['errors'] === 1 && !in_array('999', $next->getNoImageIds(), true), 'Unexpected response IDs must fail without marking products as absent.');
    $next->response = response('');
    check($next->importAllImages(null, 1, ['3'], 1)['errors'] === 1, 'Missing response rows must remain retryable.');
    $next->response = response('<row><fields><cislo>3</cislo><velobr>three.jpg</velobr><velobraz>invalid!</velobraz></fields></row>');
    $stats = $next->importAllImages(null, 1, ['3'], 1);
    check($stats['errors'] === 1 && $stats['saved'] === 0, 'Invalid image must not be reported as saved.');
    echo "PASS: persisted progress, existing WebP, absent image, unexpected IDs, empty response, invalid image.\n";
} finally {
    // Remove only the explicitly named fixture files in the random test directory.
    foreach (['cache/products.json', 'cache/no-image-ids.json', 'assets/img/produkty/one.webp'] as $file) {
        if (is_file(ROOT_PATH . '/' . $file)) {
            unlink(ROOT_PATH . '/' . $file);
        }
    }
    foreach (['assets/img/produkty', 'assets/img', 'assets', 'cache', ''] as $dir) {
        rmdir(ROOT_PATH . '/' . $dir);
    }
}
