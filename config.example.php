<?php
/**
 * ORVEX MT s.r.o. - Konfigurácia aplikácie (VZOR)
 *
 * Skopíruj tento súbor ako config.php a doplň reálne hodnoty.
 * config.php sa NEVERZUJE (je v .gitignore), pretože obsahuje citlivé
 * prístupové údaje k MRP systému.
 */

// MRP-K/S API konfigurácia
define('MRP_API_URL', 'http://xxx.xxx.xxx.xxx:8081');
define('MRP_API_LOGIN', '');
define('MRP_API_PASSWORD', '');
define('MRP_API_KEY', '');
define('MRP_AES_KEY', '');
define('MRP_HMAC_KEY', '');
define('MRP_AES_IV_LENGTH', 16);
define('MRP_COMPANY_ID', '');

// Tajny token na spustenie udrzbovych skriptov cez URL (api/cron-run.php) -
// vygeneruj vlastnu nahodnu hodnotu, napr.: php -r "echo bin2hex(random_bytes(24));"
define('CRON_SECRET', '');

// Databáza (objednávky pre admin panel)
define('DB_HOST', 'localhost');
define('DB_NAME', 'orvex_eshop');
define('DB_USER', 'root');
define('DB_PASS', '');

// Aplikácia
define('SITE_NAME', 'ORVEX Martin');
define('SITE_URL', 'https://orvex.sk');
define('COMPANY_NAME', 'ORVEX MT spol. s r.o.');
define('COMPANY_ADDRESS', 'Školská ulica 233/5');
define('COMPANY_CITY', 'Bystrička');
define('COMPANY_ZIP', '038 04');
define('COMPANY_PHONE', '+421 434 135 968');
define('COMPANY_EMAIL', 'orvex@orvex.sk');
define('COMPANY_ICO', '44596979');
define('COMPANY_DIC', '2022749168');
define('COMPANY_IC_DPH', 'SK2022749168');

// Adresa, na ktorú chodia notifikácie o nových objednávkach
define('ADMIN_NOTIFY_EMAIL', 'admin@example.com');

// SMTP (ak hosting nema funkcny system. sendmail pre PHP mail()). Port 465 =
// SMTPS (TLS od zaciatku), 587 = STARTTLS (netestovane), lokalny mail catcher
// (napr. Mailpit) zvycajne bez TLS/prihlasenia - nechaj SMTP_USER prazdne.
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 465);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// IMAP (na zapis admin notifikacie priamo do schranky ADMIN_NOTIFY_EMAIL,
// ked je to ta ista schranka ako SMTP_USER - viz appendToInbox() v mailer.php).
define('IMAP_HOST', 'imap.example.com');
define('IMAP_PORT', 993);

// Prihlasovacie udaje do /admin panelu. Hash vygeneruj prikazom:
//   php -r "echo password_hash('tvoje-heslo', PASSWORD_DEFAULT);"
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', '');

// Nastavenia
define('PRODUCTS_PER_PAGE', 12);
define('CURRENCY', '€');
define('CURRENCY_CODE', 'EUR');

// Cesty
define('ROOT_PATH', __DIR__);
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('API_PATH', ROOT_PATH . '/api');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Debug mód
define('DEBUG_MODE', false);

// MRP API
define('MRP_API_ENABLED', true);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/mailer.php';
require_once INCLUDES_PATH . '/orders.php';
