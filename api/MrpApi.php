<?php

class MrpApi
{
    private const CACHE_FILE = ROOT_PATH . '/cache/products.json';
    private const CACHE_TTL  = 86400; // 24 hodin - data sa aktualizuju raz denne cez nocny cron
    private const NO_IMAGE_FILE = ROOT_PATH . '/cache/no-image-ids.json';

    // MRP neposiela ziadnu skutocnu kategoriu produktu - pole "skupina" je v
    // skutocnosti dodavatel/vyrobca (Marko, Rys, Konsignat...), nie kategoria
    // pre e-shop. Kategoria sa preto odvodzuje z nazvu karty podla typu dielu.
    // Poradie je dolezite - prvy zhodny riadok vyhrava, konkretnejsie kluc.
    // slova musia byt pred vseobecnejsimi.
    private const CATEGORY_RULES = [
        'Ložiská'                    => ['lozisko', 'loziska'],
        'Filtre'                     => ['filter', 'filer', 'filtr'],
        'Brzdy'                      => ['brzd', 'lamela'],
        'Hydraulika a ventily'       => ['hydrog', 'ventil', 'cerpadlo', 'piest', 'valec'],
        'Elektrika'                  => ['alternator', 'spinac', 'prepinac', 'cievka', 'motorcek', 'regulator', '24v'],
        'Hriadele, čapy a prevody'   => ['hriadel', 'koleso', 'naboj', 'pastorok', 'kardan', 'poloos', 'spojka', 'unasac', 'prevod', 'cap', 'paka', 'tiahlo', 'vidlica', 'pero', 'remenica', 'sukolie'],
        'Tesnenia a krúžky'          => ['tesnenie', 'gufero', 'semering', 'manzeta', 'kruzok', 'poist', 'prilozka'],
        'Skrutky, matice a podložky' => ['skrutka', 'matica', 'podlozka', 'kolik'],
        'Pružiny'                    => ['pruzina'],
        'Hadice, rúrky a spojky'     => ['hadica', 'had.', 'spona', 'pripojka', 'objimka', 'hrdlo', 'rurka', 'rura', 'potrubie', 'priruba'],
        'Kryty, veká a puzdrá'       => ['kryt', 'veko', 'puzdro', 'skrina', 'teleso', 'zatka'],
    ];
    private const DEFAULT_CATEGORY = 'Ostatné';

    private string $apiUrl;
    private string $encKey;
    private string $authKey;
    private ?array $cachedProducts = null;

    public function __construct()
    {
        $secret = base64_decode(MRP_AES_KEY, true);
        $this->apiUrl = MRP_API_URL;
        $this->encKey  = hash_hmac('sha256', chr(1), $secret, true);
        $this->authKey = hash_hmac('sha256', $this->encKey . chr(2), $secret, true);
    }

    public function getProducts(array $filters = []): array
    {
        if ($this->cachedProducts === null) {
            $fresh = $this->loadFromCache();
            if ($fresh !== null) {
                $this->cachedProducts = $fresh;
            } else {
                try {
                    $this->cachedProducts = $this->fetchAndCache();
                } catch (\Throwable $e) {
                    // MRP nedostupne a cache je bud prazdna, alebo starsia nez
                    // CACHE_TTL. Radsej pouzijeme akekolvek (aj stare) realne
                    // data z MRP, nez aby volajuci (functions.php) spadol na
                    // natvrdo napisane demo produkty - tie sa maju zobrazit
                    // len ak naozaj nikdy nebola uspesne stiahnuta ziadna cache.
                    $stale = $this->loadFromCacheIgnoringTtl();
                    if ($stale === null) {
                        throw $e;
                    }
                    $this->cachedProducts = $stale;
                }
            }
        }

        return $this->applyFilters($this->cachedProducts, $filters);
    }

    public function clearCache(): void
    {
        $this->cachedProducts = null;
        if (file_exists(self::CACHE_FILE)) {
            unlink(self::CACHE_FILE);
        }
    }

    /**
     * Vynuti nove stiahnutie z MRP (na rozdiel od clearCache()+getProducts()
     * NEMAZE najprv stary cache subor). Ak MRP zlyha, povodne platne data
     * ostanu nedotknute - web tak nikdy nezostane bez dat len preto, ze sa
     * nepodaril pokus o obnovenie (cron beziaci kazdych 15 min by inak mohol
     * kedykoli "vynulovat" cache presne vo chvili, ked je MRP docasne dole).
     */
    public function refreshProducts(): array
    {
        $this->cachedProducts = null;
        return $this->fetchAndCache();
    }

    /**
     * Vrati zoznam cisel kariet, o ktorych uz vieme (z predchadzajucich behov
     * importAllImages), ze v MRP nemaju ziadnu fotku - netreba ich znova pytat.
     */
    public function getNoImageIds(): array
    {
        if (!file_exists(self::NO_IMAGE_FILE)) {
            return [];
        }
        $ids = json_decode(file_get_contents(self::NO_IMAGE_FILE), true);
        return is_array($ids) ? $ids : [];
    }

    private function rememberNoImageIds(array $newIds): void
    {
        $existing = $this->getNoImageIds();
        $merged = array_values(array_unique(array_merge($existing, $newIds)));
        file_put_contents(self::NO_IMAGE_FILE, json_encode($merged));
    }

    private function loadFromCache(): ?array
    {
        if (!file_exists(self::CACHE_FILE)) {
            return null;
        }
        if (time() - filemtime(self::CACHE_FILE) > self::CACHE_TTL) {
            return null;
        }
        return $this->loadFromCacheIgnoringTtl();
    }

    private function loadFromCacheIgnoringTtl(): ?array
    {
        if (!file_exists(self::CACHE_FILE)) {
            return null;
        }
        $data = json_decode(file_get_contents(self::CACHE_FILE), true);
        return is_array($data) ? $data : null;
    }

    private function fetchAndCache(): array
    {
        $xml      = $this->sendCommand('EXPEO0');
        $products = $this->parseProducts($xml);

        $cacheDir = dirname(self::CACHE_FILE);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        file_put_contents(self::CACHE_FILE, json_encode($products, JSON_UNESCAPED_UNICODE));

        return $products;
    }

    public function getProduct(string $productId): ?array
    {
        foreach ($this->getProducts() as $product) {
            if ($product['id'] === $productId) {
                return $product;
            }
        }
        return null;
    }

    public function getCategories(): array
    {
        $categories = [];
        foreach ($this->getProducts() as $product) {
            $id = $product['category_id'];
            if (!$id) continue;
            if (!isset($categories[$id])) {
                $categories[$id] = [
                    'id'        => $id,
                    'name'      => $product['category'],
                    'parent_id' => '',
                    'count'     => 0,
                    'image'     => '',
                ];
            }
            $categories[$id]['count']++;
            // Ako reprezentativny obrazok kategorie sa berie obrazok prveho
            // produktu v nej, ktory nejaky ma (vela kariet z MRP fotku nema).
            if ($categories[$id]['image'] === '' && !empty($product['image'])) {
                $categories[$id]['image'] = $product['image'];
            }
        }
        return array_values($categories);
    }

    public function submitOrder(array $orderData): array
    {
        return ['success' => false, 'message' => 'Odosielanie objednávok nie je implementované'];
    }

    /**
     * Hromadne stiahne velke obrazky (velobraz) pre vsetky eshop produkty a ulozi
     * ich lokalne (vratane WebP konverzie cez saveImage/convertToWebp).
     *
     * Stiahnutie vsetkych produktov naraz s obrazkami padne na MRP serveri
     * (nedostatok pamate), preto sa produkty posielaju po malych davkach cez
     * filter SKKAR.CISLO (presny zoznam cisel kariet oddeleny "|").
     *
     * Nevola sa automaticky pri beznom obnoveni cache (fetchAndCache) - je to
     * pomala, jednorazova/prilezitostna operacia urcena na manualne spustenie.
     */
    public function importAllImages(?callable $onProgress = null, int $batchSize = 50, ?array $onlyIds = null, ?int $maxBatches = null): array
    {
        $ids = $onlyIds ?? array_column($this->getProducts(), 'id');
        $batches = array_chunk($ids, $batchSize);
        if ($maxBatches !== null) {
            $batches = array_slice($batches, 0, $maxBatches);
        }

        $stats = ['batches' => count($batches), 'done' => 0, 'saved' => 0, 'skipped' => 0, 'errors' => 0];
        $confirmedNoImage = [];

        foreach ($batches as $batch) {
            try {
                $xml = $this->sendCommand('EXPEO0', [
                    'velObraz'     => 'T',
                    'SKKAR.CISLO'  => implode('|', $batch),
                ], 120);

                if (isset($xml->data->datasets->karty->rows->row)) {
                    foreach ($xml->data->datasets->karty->rows->row as $row) {
                        $f = $row->fields;
                        $velobr   = trim((string)$f->velobr);
                        $velobraz = (string)$f->velobraz;

                        if ($velobr === '' || $velobraz === '') {
                            $stats['skipped']++;
                            // MRP pre tuto kartu nema ziadnu fotku - zapamatame si to trvalo,
                            // aby ju dalsie behy uz znova neoverovali (bez toho by kazdy beh
                            // strácal cast svojho maleho casoveho rozpoctu na tie iste "prazdne" karty).
                            $confirmedNoImage[] = (string)$f->cislo;
                            continue;
                        }

                        $path = ROOT_PATH . '/assets/img/produkty/' . basename($velobr);
                        $alreadyExisted = file_exists($path);

                        $this->saveImage($velobraz, $velobr);

                        if ($alreadyExisted) {
                            $stats['skipped']++;
                        } else {
                            $stats['saved']++;
                        }
                    }
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                if ($onProgress) {
                    $onProgress($stats, 'Chyba davky: ' . $e->getMessage());
                }
                continue;
            }

            $stats['done']++;
            if ($onProgress) {
                $onProgress($stats, null);
            }

            // Uklada sa po kazdej davke (nie az na konci) - proces moze byt
            // predcasne ukonceny (napr. limitom hostingu na dlho bezice CLI
            // procesy), a nechceme prist o uz zistene "bez fotky" karty.
            if (!empty($confirmedNoImage)) {
                $this->rememberNoImageIds($confirmedNoImage);
                $confirmedNoImage = [];
            }
        }

        return $stats;
    }

    private function sendCommand(string $command, array $filterOverrides = [], int $timeoutSeconds = 30): SimpleXMLElement
    {
        $filters = array_merge([
            'stavy'     => 'F',
            // Obrazky sa defaultne nestahuju - export vsetkych 1600+ kariet naraz
            // s obrazkami padne na MRP serveri na EOleException (nedostatok pamate).
            // Pre hromadne stiahnutie obrazkov pozri importAllImages(), ktora si
            // pyta velObraz=T len po malych davkach cez filter SKKAR.CISLO.
            'malObraz'  => 'F',
            'velObraz'  => 'F',
            // ciskat = cislo katalogovej skupiny (nie boolovsky priznak).
            // V MRP je katalogova skupina 1 nazvana "eshop", preto tu ziadame
            // od servera len produkty priradene do tejto skupiny.
            'ciskat'    => '1',
        ], $filterOverrides);

        $filterXml = '';
        foreach ($filters as $name => $value) {
            $filterXml .= '<fltvalue name="' . htmlspecialchars((string)$name, ENT_XML1) . '">'
                . htmlspecialchars((string)$value, ENT_XML1) . '</fltvalue>';
        }

        // requestId musi byt unikatne aj pri viacerych volaniach v tej istej sekunde
        // (napr. davkovy import obrazkov) - MRP pri zhode requestId len vrati kopiu
        // predchadzajucej odpovede namiesto vykonania noveho pozadavku.
        $requestId = 'req-' . time() . '-' . bin2hex(random_bytes(4));

        $requestXml = '<?xml version="1.0" encoding="windows-1250"?>'
            . '<mrpRequest>'
            . '<request command="' . htmlspecialchars($command, ENT_XML1) . '" requestId="' . $requestId . '"/>'
            . '<data>'
            . '<filter>' . $filterXml . '</filter>'
            . '</data>'
            . '</mrpRequest>';

        $requestBytes = iconv('UTF-8', 'Windows-1250//TRANSLIT', $requestXml);

        $varKey       = random_bytes(32);
        $finalEncKey  = hash_hmac('sha256', $varKey, $this->encKey, true);
        $iv           = substr(hash('sha256', $varKey, true), 0, 16);

        $encryptedData = openssl_encrypt($requestBytes, 'aes-256-ctr', $finalEncKey, OPENSSL_RAW_DATA, $iv);

        $paramsXml  = '<mrpEncodingParams encryption="aes"><varKey>' . base64_encode($varKey) . '</varKey></mrpEncodingParams>';
        $paramsBytes = iconv('UTF-8', 'Windows-1250//TRANSLIT', $paramsXml);
        $authCode    = hash_hmac('sha256', $paramsBytes . $encryptedData, $this->authKey, true);

        $postXml = '<?xml version="1.0" encoding="windows-1250"?>'
            . '<mrpEnvelope>'
            . '<encodedBody authentication="hmac_sha256">'
            . '<encodingParams><![CDATA[' . base64_encode($paramsBytes) . ']]></encodingParams>'
            . '<encodedData><![CDATA[' . base64_encode($encryptedData) . ']]></encodedData>'
            . '<authCode><![CDATA[' . base64_encode($authCode) . ']]></authCode>'
            . '</encodedBody>'
            . '</mrpEnvelope>';

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST          => true,
            CURLOPT_POSTFIELDS    => iconv('UTF-8', 'Windows-1250//TRANSLIT', $postXml),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT       => $timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER    => ['Content-Type: application/xml; charset=windows-1250'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            throw new RuntimeException('Chyba pripojenia k MRP: ' . $error);
        }
        if ($httpCode !== 200) {
            throw new RuntimeException('MRP API HTTP chyba: ' . $httpCode);
        }

        $decrypted = $this->decryptResponse($response);

        // LIBXML_PARSEHUGE - odpovede s obrazkami (base64 v CDATA) mozu byt desiatky MB,
        // bez tohto flagu libxml parsovanie takto velkych uzlov odmietne.
        $xml = simplexml_load_string($decrypted, SimpleXMLElement::class, LIBXML_PARSEHUGE);
        if ($xml === false) {
            throw new RuntimeException('Neplatná XML odpoveď z MRP');
        }

        return $xml;
    }

    private function decryptResponse(string $response): string
    {
        $xml = simplexml_load_string($response, SimpleXMLElement::class, LIBXML_PARSEHUGE);

        if (!$xml || !isset($xml->encodedBody)) {
            return $response;
        }

        $paramsBytes   = base64_decode((string)$xml->encodedBody->encodingParams, true);
        $encryptedData = base64_decode((string)$xml->encodedBody->encodedData, true);
        $authCode      = base64_decode((string)$xml->encodedBody->authCode, true);

        $expected = hash_hmac('sha256', $paramsBytes . $encryptedData, $this->authKey, true);
        if (!hash_equals($expected, $authCode)) {
            throw new RuntimeException('Neplatný HMAC v odpovedi MRP');
        }

        $paramsXml   = simplexml_load_string($paramsBytes);
        $varKey      = base64_decode((string)$paramsXml->varKey, true);
        $finalEncKey = hash_hmac('sha256', $varKey, $this->encKey, true);
        $iv          = substr(hash('sha256', $varKey, true), 0, 16);

        $plain = openssl_decrypt($encryptedData, 'aes-256-ctr', $finalEncKey, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            throw new RuntimeException('Dešifrovanie odpovede MRP zlyhalo');
        }

        // MRP posiela decryptnutu odpoved uz v UTF-8 (napriek deklaracii windows-1250
        // v poziadavke) - dalsi iconv z windows-1250 do UTF-8 by uz spravne zakodovane
        // znaky poskodil dvojitou konverziou (napr. "Ĺ tevaĹĂˇk" namiesto "Števaňák").
        return $plain;
    }

    private function parseProducts(SimpleXMLElement $xml): array
    {
        $products = [];

        if (!isset($xml->data->datasets->karty->rows->row)) {
            return $products;
        }

        foreach ($xml->data->datasets->karty->rows->row as $row) {
            $f = $row->fields;

            // ciskat/ciskatlist = katalogova skupina (skupiny), do ktorej je karta zaradena
            // v MRP (nie priznak "tlacit"). Skupina 1 = "eshop" v cislenniku <katalog>.
            // Server uz filtruje na ciskat=1 (viz sendCommand), tu je len poistka.
            $ciskat     = trim((string)$f->ciskat);
            $ciskatlist = array_map('trim', explode('|', (string)$f->ciskatlist));
            if ($ciskat !== '1' && !in_array('1', $ciskatlist, true)) {
                continue;
            }

            $categoryName = $this->resolveCategory((string)$f->nazev);
            $categoryId   = $this->slugify($categoryName);

            $malobr = trim((string)$f->malobr);
            $velobr = trim((string)$f->velobr);

            $this->saveImage((string)$f->malobraz, $malobr);
            $this->saveImage((string)$f->velobraz, $velobr);

            $imgPath = [$this, 'resolveImagePath'];
            $images = array_values(array_unique(array_filter([$imgPath($malobr), $imgPath($velobr)])));

            // MRP posiela popis rozdeleny do troch poli (kratky popis, poznamka,
            // dlhy popis) - kazda karta ma vyplnene len niektore z nich, preto ich
            // skladame dokopy namiesto pouzitia len jedneho (predtym len "poznamka",
            // co pokrylo len ~54% produktov aj ked realny text existoval aj inde).
            $descriptionParts = array_unique(array_filter([
                trim((string)$f->malpopis),
                trim((string)$f->poznamka),
                trim((string)$f->velpopis),
            ], fn($part) => $part !== ''));

            $products[] = [
                'id'          => (string)$f->cislo,
                'name'        => (string)$f->nazev,
                'description' => implode("\n\n", $descriptionParts),
                'price'       => (float)$f->cena,
                'price_vat'   => (float)$f->cenasdph,
                'vat_rate'    => (float)$f->sazbadph,
                'sku'         => (string)$f->kod1,
                'category'    => $categoryName,
                'category_id' => $categoryId,
                'stock'       => (int)$f->pocetmj,
                'unit'        => (string)$f->jednotka ?: 'ks',
                'image'       => $imgPath($malobr),
                'images'      => $images,
                'params'      => [],
            ];
        }

        return $products;
    }

    private function slugify(string $text): string
    {
        $transliterated = strtr($text, [
            'á' => 'a', 'ä' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'í' => 'i',
            'ľ' => 'l', 'ĺ' => 'l', 'ň' => 'n', 'ó' => 'o', 'ô' => 'o', 'ŕ' => 'r',
            'š' => 's', 'ť' => 't', 'ú' => 'u', 'ý' => 'y', 'ž' => 'z',
            'Á' => 'A', 'Ä' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E', 'Í' => 'I',
            'Ľ' => 'L', 'Ĺ' => 'L', 'Ň' => 'N', 'Ó' => 'O', 'Ô' => 'O', 'Ŕ' => 'R',
            'Š' => 'S', 'Ť' => 'T', 'Ú' => 'U', 'Ý' => 'Y', 'Ž' => 'Z',
        ]);

        return trim(strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $transliterated)), '-');
    }

    private function resolveCategory(string $productName): string
    {
        $name = mb_strtolower($productName, 'UTF-8');
        foreach (self::CATEGORY_RULES as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($name, $keyword)) {
                    return $category;
                }
            }
        }
        return self::DEFAULT_CATEGORY;
    }

    private function resolveImagePath(string $name): string
    {
        if ($name === '') {
            return '';
        }
        $base = basename($name);
        $webpRelative = 'assets/img/produkty/' . preg_replace('/\.[^.]+$/', '', $base) . '.webp';
        if (file_exists(ROOT_PATH . '/' . $webpRelative)) {
            return $webpRelative;
        }
        $relativePath = 'assets/img/produkty/' . $base;
        return file_exists(ROOT_PATH . '/' . $relativePath) ? $relativePath : '';
    }

    private function convertToWebp(string $absolutePath): bool
    {
        $webpPath = preg_replace('/\.[^.]+$/', '', $absolutePath) . '.webp';
        if (file_exists($webpPath)) {
            return true;
        }
        if (!function_exists('imagewebp')) {
            return false;
        }

        $info = @getimagesize($absolutePath);
        if ($info === false) {
            return false;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG  => @imagecreatefrompng($absolutePath),
            IMAGETYPE_GIF  => @imagecreatefromgif($absolutePath),
            default        => false,
        };
        if ($image === false) {
            return false;
        }

        // Zmensenie na max. 1000 px na dlhsej strane - produktove fotky z MRP
        // byvaju vela MB velke, web taku velkost/kvalitu nepotrebuje.
        $width  = imagesx($image);
        $height = imagesy($image);
        $maxDim = 1000;
        if ($width > $maxDim || $height > $maxDim) {
            $ratio     = min($maxDim / $width, $maxDim / $height);
            $newWidth  = max(1, (int) round($width * $ratio));
            $newHeight = max(1, (int) round($height * $ratio));
            $resized   = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $image = $resized;
        }

        // imagedestroy() sa v PHP 8+ nevola - GC uvolni pamat automaticky, funkcia
        // je uz len no-op a od 8.5 generuje deprecation warning.
        return imagewebp($image, $webpPath, 82);
    }

    private function saveImage(string $base64data, string $filename): void
    {
        if ($base64data === '' || $filename === '') {
            return;
        }

        $dir  = ROOT_PATH . '/assets/img/produkty';
        $path = $dir . '/' . basename($filename);

        if (file_exists($path)) {
            $this->convertToWebp($path);
            return;
        }

        $bytes = base64_decode($base64data, true);
        if ($bytes === false || $bytes === '') {
            return;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, $bytes);
        $this->convertToWebp($path);
    }

    private function applyFilters(array $products, array $filters): array
    {
        if (!empty($filters['search'])) {
            $search = mb_strtolower($filters['search'], 'UTF-8');
            $products = array_filter($products, function ($p) use ($search) {
                return str_contains(mb_strtolower($p['name'], 'UTF-8'), $search)
                    || str_contains(mb_strtolower($p['sku'], 'UTF-8'), $search)
                    || str_contains(mb_strtolower($p['description'], 'UTF-8'), $search);
            });
        }

        if (!empty($filters['category'])) {
            $products = array_filter($products, fn($p) => $p['category_id'] === $filters['category']);
        }

        return array_values($products);
    }
}
