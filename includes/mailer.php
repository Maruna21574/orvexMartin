<?php

function getEmailHeader(): string
{
    // orvex_logo_white.png je vygenerovana biela verzia loga (z orvex_logo_hl.png,
    // ktory je tmavozeleny) - predtym sa na biely vzhlad spolahalo na CSS
    // filter:invert(), ktory Gmail (a dalsi mailovi klienti) v HTML mailoch
    // ignoruju/odstranuju, cim sa zobrazovalo povodne tmavozelene logo.
    $logoUrl = SITE_URL . '/assets/images/orvex_logo_white.png';
    return '
    <div style="background:#1a5632;padding:20px 32px;text-align:center;">
        <img src="' . $logoUrl . '" alt="' . COMPANY_NAME . '" style="height:44px;width:auto;">
    </div>';
}

function getEmailFooter(): string
{
    return '
    <div style="background:#f9fafb;padding:24px 32px;border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:#9ca3af;font-family:Arial,sans-serif;">
        <p style="margin:0 0 4px;">' . COMPANY_NAME . ' | ' . COMPANY_ADDRESS . ', ' . COMPANY_ZIP . ' ' . COMPANY_CITY . '</p>
        <p style="margin:0 0 4px;">IČO: ' . COMPANY_ICO . ' | DIČ: ' . COMPANY_DIC . '</p>
        <p style="margin:0;">Tel.: ' . COMPANY_PHONE . ' | ' . COMPANY_EMAIL . '</p>
    </div>';
}

function buildEmailHtml(string $bodyHtml): string
{
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;">';
    $html .= '<div style="max-width:600px;margin:24px auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,0.06);">';
    $html .= getEmailHeader();
    $html .= '<div style="padding:32px;">';
    $html .= $bodyHtml;
    $html .= '</div>';
    $html .= getEmailFooter();
    $html .= '</div>';
    $html .= '</body></html>';
    return $html;
}

function sendHtmlEmail(string $to, string $subject, string $bodyHtml, ?string $replyTo = null): bool
{
    $headers = "From: " . COMPANY_NAME . " <" . SMTP_USER . ">\r\n";
    $headers .= "To: <" . $to . ">\r\n";
    $headers .= "Reply-To: " . ($replyTo ?? COMPANY_EMAIL) . "\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $headers .= "Date: " . date('r') . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    return sendViaSmtp($to, $headers . "\r\n" . buildEmailHtml($bodyHtml));
}

/**
 * Notifikacie adminovi chodia na tu istu schranku (SMTP_USER), z ktorej sa aj
 * odosiela - "mail sam sebe" ale Websupport (a bezne mailservery vseobecne)
 * takéto self-send spravy potichu zahadzuju ako anti-loop ochranu (aj ked
 * SMTP prijme "250 OK", sprava sa nikdy nedoruci). Preto sa namiesto SMTP
 * odoslania sprava zapisuje priamo do schranky cez IMAP APPEND - to nie je
 * "odoslanie" v zmysle mail transportu, takze sa anti-loop ochrana neuplatni.
 */
function appendToInbox(string $subject, string $bodyHtml): bool
{
    $rawMessage = "From: " . COMPANY_NAME . " <" . SMTP_USER . ">\r\n";
    $rawMessage .= "To: <" . SMTP_USER . ">\r\n";
    $rawMessage .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $rawMessage .= "Date: " . date('r') . "\r\n";
    $rawMessage .= "Message-ID: <" . uniqid('', true) . "@" . (parse_url(SITE_URL, PHP_URL_HOST) ?: 'orvex.sk') . ">\r\n";
    $rawMessage .= "MIME-Version: 1.0\r\n";
    $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n";
    $rawMessage .= "\r\n";
    $rawMessage .= buildEmailHtml($bodyHtml);

    $socket = @stream_socket_client('ssl://' . IMAP_HOST . ':' . IMAP_PORT, $errno, $errstr, 15);
    if ($socket === false) {
        error_log('IMAP: spojenie zlyhalo - ' . $errstr . ' (' . $errno . ')');
        return false;
    }
    stream_set_timeout($socket, 15);

    $readUntilTagged = function (string $tag) use ($socket): string {
        $resp = '';
        while (!feof($socket)) {
            $line = fgets($socket, 8192);
            if ($line === false) {
                break;
            }
            $resp .= $line;
            if (str_starts_with($line, $tag)) {
                break;
            }
        }
        return $resp;
    };
    $imapQuote = fn (string $v): string => '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $v) . '"';

    fgets($socket, 8192); // greeting

    fwrite($socket, 'A1 LOGIN ' . $imapQuote(SMTP_USER) . ' ' . $imapQuote(SMTP_PASS) . "\r\n");
    $resp = $readUntilTagged('A1');
    if (!str_contains($resp, 'A1 OK')) {
        error_log('IMAP: prihlasenie zlyhalo - ' . $resp);
        fclose($socket);
        return false;
    }

    fwrite($socket, 'A2 APPEND INBOX (\\Seen) {' . strlen($rawMessage) . "}\r\n");
    $cont = fgets($socket, 8192);
    if ($cont === false || !str_starts_with(trim($cont), '+')) {
        error_log('IMAP: APPEND continuation zlyhala - ' . $cont);
        fclose($socket);
        return false;
    }

    fwrite($socket, $rawMessage . "\r\n");
    $resp = $readUntilTagged('A2');

    fwrite($socket, "A3 LOGOUT\r\n");
    fclose($socket);

    if (!str_contains($resp, 'A2 OK')) {
        error_log('IMAP: APPEND zlyhal - ' . $resp);
        return false;
    }

    return true;
}

/**
 * Odosle mail priamo cez SMTP (bez zavislosti na kniznici ako PHPMailer - v
 * stajle zvysku projektu, ktory pouziva len holé sockety/cURL, napr. MrpApi).
 * Dovod existencie: na tomto hostingu chyba systemovy "sendmail", takze PHP
 * funkcia mail() vzdy zlyha ("Sendmail exited with non-zero exit code 127").
 */
function sendViaSmtp(string $to, string $rawMessage): bool
{
    $readResponse = function ($socket): array {
        $lines = [];
        while (!feof($socket)) {
            $line = fgets($socket, 515);
            if ($line === false) {
                break;
            }
            $lines[] = $line;
            // Viacriadkova SMTP odpoved ma na poslednom riadku za kodom medzeru
            // (napr. "250 OK"), na predchadzajucich pomlcku ("250-...").
            if (!isset($line[3]) || $line[3] !== '-') {
                break;
            }
        }
        $code = $lines ? (int) substr($lines[0], 0, 3) : 0;
        return [$code, implode('', $lines)];
    };

    // Port 465 = SMTPS (sifrovanie od prveho bajtu). Lokalne (Mailpit, port
    // 1025) sa pripaja bez sifrovania a bez prihlasenia - SMTP_USER je prazdne.
    $useTls = (int) SMTP_PORT === 465;
    $requiresAuth = SMTP_USER !== '';

    $errno = 0;
    $errstr = '';
    $socket = @stream_socket_client(
        ($useTls ? 'ssl://' : '') . SMTP_HOST . ':' . SMTP_PORT,
        $errno,
        $errstr,
        15
    );
    if ($socket === false) {
        error_log('SMTP: spojenie zlyhalo - ' . $errstr . ' (' . $errno . ')');
        return false;
    }
    stream_set_timeout($socket, 15);

    $sendCommand = function (string $command) use ($socket, $readResponse): array {
        fwrite($socket, $command . "\r\n");
        return $readResponse($socket);
    };

    [$code, $resp] = $readResponse($socket);
    if ($code !== 220) {
        error_log('SMTP: neocakavany banner - ' . $resp);
        fclose($socket);
        return false;
    }

    $ehloHost = parse_url(SITE_URL, PHP_URL_HOST) ?: 'localhost';
    [$code, $resp] = $sendCommand('EHLO ' . $ehloHost);
    if ($code !== 250) {
        error_log('SMTP: EHLO zlyhalo - ' . $resp);
        fclose($socket);
        return false;
    }

    if ($requiresAuth) {
        [$code, $resp] = $sendCommand('AUTH LOGIN');
        if ($code !== 334) {
            error_log('SMTP: AUTH LOGIN zlyhalo - ' . $resp);
            fclose($socket);
            return false;
        }

        [$code, $resp] = $sendCommand(base64_encode(SMTP_USER));
        if ($code !== 334) {
            error_log('SMTP: odoslanie mena zlyhalo - ' . $resp);
            fclose($socket);
            return false;
        }

        [$code, $resp] = $sendCommand(base64_encode(SMTP_PASS));
        if ($code !== 235) {
            error_log('SMTP: prihlasenie zlyhalo - ' . $resp);
            fclose($socket);
            return false;
        }
    }

    $envelopeFrom = $requiresAuth ? SMTP_USER : 'noreply@' . $ehloHost;
    [$code, $resp] = $sendCommand('MAIL FROM:<' . $envelopeFrom . '>');
    if ($code !== 250) {
        error_log('SMTP: MAIL FROM zlyhalo - ' . $resp);
        fclose($socket);
        return false;
    }

    [$code, $resp] = $sendCommand('RCPT TO:<' . $to . '>');
    if ($code !== 250) {
        error_log('SMTP: RCPT TO zlyhalo - ' . $resp);
        fclose($socket);
        return false;
    }

    [$code, $resp] = $sendCommand('DATA');
    if ($code !== 354) {
        error_log('SMTP: DATA zlyhalo - ' . $resp);
        fclose($socket);
        return false;
    }

    // Dot-stuffing - riadok zacinajuci bodkou by SMTP inak povazoval za koniec spravy.
    $escaped = preg_replace('/^\./m', '..', $rawMessage);
    fwrite($socket, $escaped . "\r\n.\r\n");
    [$code, $resp] = $readResponse($socket);
    if ($code !== 250) {
        error_log('SMTP: odoslanie spravy zlyhalo - ' . $resp);
        fclose($socket);
        return false;
    }

    fwrite($socket, "QUIT\r\n");
    fclose($socket);

    return true;
}

function sendContactConfirmation(string $name, string $email, string $subject, string $message): void
{
    $subjectMap = [
        'dopyt' => 'Dopyt na produkt',
        'cenova-ponuka' => 'Cenová ponuka',
        'nahradne-diely' => 'Náhradné diely',
        'reklamacia' => 'Reklamácia',
        'ine' => 'Iné',
    ];
    $subjectText = $subjectMap[$subject] ?? 'Kontaktný formulár';

    $customerBody = '
        <h2 style="margin:0 0 8px;font-size:20px;color:#111827;">Ďakujeme za vašu správu</h2>
        <p style="margin:0 0 24px;color:#6b7280;font-size:14px;">Prijali sme vašu správu a ozveme sa vám čo najskôr.</p>

        <div style="background:#f0f7f3;border-radius:8px;padding:20px;margin-bottom:24px;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;font-family:Arial,sans-serif;">
                <tr>
                    <td style="padding:6px 0;color:#6b7280;width:100px;">Meno:</td>
                    <td style="padding:6px 0;color:#111827;font-weight:500;">' . htmlspecialchars($name) . '</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Predmet:</td>
                    <td style="padding:6px 0;color:#111827;font-weight:500;">' . htmlspecialchars($subjectText) . '</td>
                </tr>
            </table>
        </div>

        <div style="background:#f9fafb;border-left:3px solid #1a5632;padding:16px;border-radius:0 8px 8px 0;margin-bottom:24px;">
            <p style="margin:0 0 4px;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Vaša správa:</p>
            <p style="margin:0;font-size:14px;color:#374151;line-height:1.6;white-space:pre-line;">' . htmlspecialchars($message) . '</p>
        </div>

        <p style="margin:0;font-size:13px;color:#6b7280;">Ak máte ďalšie otázky, neváhajte nás kontaktovať na <a href="mailto:' . COMPANY_EMAIL . '" style="color:#1a5632;">' . COMPANY_EMAIL . '</a> alebo na tel. č. ' . COMPANY_PHONE . '.</p>';

    sendHtmlEmail($email, 'Prijali sme vašu správu – ' . COMPANY_NAME, $customerBody);

    $adminBody = '
        <h2 style="margin:0 0 8px;font-size:20px;color:#111827;">Nová správa z kontaktného formulára</h2>
        <p style="margin:0 0 24px;color:#6b7280;font-size:14px;">' . date('d.m.Y H:i') . '</p>

        <table style="width:100%;border-collapse:collapse;font-size:14px;font-family:Arial,sans-serif;margin-bottom:24px;">
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:10px 0;color:#6b7280;width:120px;">Meno:</td>
                <td style="padding:10px 0;color:#111827;font-weight:600;">' . htmlspecialchars($name) . '</td>
            </tr>
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:10px 0;color:#6b7280;">E-mail:</td>
                <td style="padding:10px 0;"><a href="mailto:' . htmlspecialchars($email) . '" style="color:#1a5632;font-weight:500;">' . htmlspecialchars($email) . '</a></td>
            </tr>
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:10px 0;color:#6b7280;">Predmet:</td>
                <td style="padding:10px 0;color:#111827;">' . htmlspecialchars($subjectText) . '</td>
            </tr>
        </table>

        <div style="background:#f9fafb;border-left:3px solid #1a5632;padding:16px;border-radius:0 8px 8px 0;margin-bottom:24px;">
            <p style="margin:0 0 4px;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Správa:</p>
            <p style="margin:0;font-size:14px;color:#374151;line-height:1.6;white-space:pre-line;">' . htmlspecialchars($message) . '</p>
        </div>

        <a href="mailto:' . htmlspecialchars($email) . '" style="display:inline-block;background:#1a5632;color:#fff;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:500;">Odpovedať zákazníkovi</a>';

    sendHtmlEmail(COMPANY_EMAIL, 'Web: ' . $subjectText . ' – ' . $name, $adminBody);
}

function sendOrderConfirmation(string $orderNumber, array $orderData, array $cart, float $total): void
{
    $itemsHtml = '';
    foreach ($cart as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $itemsHtml .= '
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:10px 0;font-size:14px;color:#111827;">' . htmlspecialchars($item['name']) . '<br><span style="font-size:12px;color:#6b7280;">' . htmlspecialchars($item['sku']) . '</span></td>
                <td style="padding:10px 0;font-size:14px;color:#6b7280;text-align:center;">' . $item['quantity'] . '</td>
                <td style="padding:10px 0;font-size:14px;color:#111827;text-align:right;font-weight:500;">' . formatPrice($itemTotal) . '</td>
            </tr>';
    }

    $addressHtml = htmlspecialchars($orderData['name']);
    if (!empty($orderData['company'])) {
        $addressHtml .= '<br>' . htmlspecialchars($orderData['company']);
    }
    $addressHtml .= '<br>' . htmlspecialchars($orderData['street']);
    $addressHtml .= '<br>' . htmlspecialchars($orderData['zip']) . ' ' . htmlspecialchars($orderData['city']);

    $customerBody = '
        <h2 style="margin:0 0 8px;font-size:20px;color:#111827;">Ďakujeme za vašu objednávku!</h2>
        <p style="margin:0 0 24px;color:#6b7280;font-size:14px;">Vaša objednávka bola prijatá a budeme vás informovať o jej stave.</p>

        <div style="background:#f0f7f3;border-radius:8px;padding:16px 20px;margin-bottom:24px;text-align:center;">
            <p style="margin:0 0 4px;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Číslo objednávky</p>
            <p style="margin:0;font-size:24px;font-weight:700;color:#1a5632;">' . htmlspecialchars($orderNumber) . '</p>
        </div>

        <h3 style="margin:0 0 12px;font-size:16px;color:#111827;">Objednané položky</h3>
        <table style="width:100%;border-collapse:collapse;font-family:Arial,sans-serif;margin-bottom:16px;">
            <tr style="border-bottom:2px solid #e5e7eb;">
                <th style="padding:8px 0;font-size:12px;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:0.05em;">Produkt</th>
                <th style="padding:8px 0;font-size:12px;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:0.05em;">Množstvo</th>
                <th style="padding:8px 0;font-size:12px;color:#6b7280;text-align:right;text-transform:uppercase;letter-spacing:0.05em;">Cena</th>
            </tr>
            ' . $itemsHtml . '
            <tr>
                <td colspan="2" style="padding:14px 0;font-size:16px;font-weight:700;color:#111827;">Celkom s DPH</td>
                <td style="padding:14px 0;font-size:18px;font-weight:700;color:#1a5632;text-align:right;">' . formatPrice($total) . '</td>
            </tr>
        </table>

        <div style="margin-bottom:24px;">
            <h3 style="margin:0 0 8px;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Adresa doručenia</h3>
            <p style="margin:0 0 16px;font-size:14px;color:#111827;line-height:1.6;">' . $addressHtml . '</p>
            <h3 style="margin:0 0 8px;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Kontakt</h3>
            <p style="margin:0;font-size:14px;color:#111827;line-height:1.6;">' . htmlspecialchars($orderData['email']) . '<br>' . htmlspecialchars($orderData['phone']) . '</p>
        </div>' .

        (!empty($orderData['note']) ? '
        <div style="background:#f9fafb;border-left:3px solid #1a5632;padding:12px 16px;border-radius:0 8px 8px 0;margin-bottom:24px;">
            <p style="margin:0 0 4px;font-size:12px;color:#6b7280;">Poznámka:</p>
            <p style="margin:0;font-size:14px;color:#374151;">' . htmlspecialchars($orderData['note']) . '</p>
        </div>' : '') . '

        <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.6;">Cena dopravy bude stanovená individuálne. Faktúru vám zašleme na uvedený email. V prípade otázok nás kontaktujte na <a href="mailto:' . COMPANY_EMAIL . '" style="color:#1a5632;">' . COMPANY_EMAIL . '</a> alebo na tel. č. ' . COMPANY_PHONE . '.</p>';

    sendHtmlEmail($orderData['email'], 'Potvrdenie objednávky ' . $orderNumber . ' – ' . COMPANY_NAME, $customerBody, ADMIN_NOTIFY_EMAIL);

    $adminBody = '
        <h2 style="margin:0 0 4px;font-size:20px;color:#111827;">Nová objednávka</h2>
        <p style="margin:0 0 24px;color:#6b7280;font-size:14px;">' . date('d.m.Y H:i') . '</p>

        <div style="background:#f0f7f3;border-radius:8px;padding:16px 20px;margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <p style="margin:0;font-size:12px;color:#6b7280;">Číslo objednávky</p>
                <p style="margin:0;font-size:20px;font-weight:700;color:#1a5632;">' . htmlspecialchars($orderNumber) . '</p>
            </div>
            <div style="text-align:right;">
                <p style="margin:0;font-size:12px;color:#6b7280;">Celkom s DPH</p>
                <p style="margin:0;font-size:20px;font-weight:700;color:#111827;">' . formatPrice($total) . '</p>
            </div>
        </div>

        <h3 style="margin:0 0 12px;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Zákazník</h3>
        <table style="width:100%;border-collapse:collapse;font-size:14px;font-family:Arial,sans-serif;margin-bottom:24px;">
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:8px 0;color:#6b7280;width:120px;">Meno:</td>
                <td style="padding:8px 0;color:#111827;font-weight:600;">' . htmlspecialchars($orderData['name']) . '</td>
            </tr>' .
            (!empty($orderData['company']) ? '
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:8px 0;color:#6b7280;">Firma:</td>
                <td style="padding:8px 0;color:#111827;">' . htmlspecialchars($orderData['company']) . '</td>
            </tr>' : '') .
            (!empty($orderData['ico']) ? '
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:8px 0;color:#6b7280;">IČO / DIČ:</td>
                <td style="padding:8px 0;color:#111827;">' . htmlspecialchars($orderData['ico']) . (!empty($orderData['dic']) ? ' / ' . htmlspecialchars($orderData['dic']) : '') . '</td>
            </tr>' : '') . '
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:8px 0;color:#6b7280;">E-mail:</td>
                <td style="padding:8px 0;"><a href="mailto:' . htmlspecialchars($orderData['email']) . '" style="color:#1a5632;">' . htmlspecialchars($orderData['email']) . '</a></td>
            </tr>
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:8px 0;color:#6b7280;">Telefón:</td>
                <td style="padding:8px 0;color:#111827;">' . htmlspecialchars($orderData['phone']) . '</td>
            </tr>
            <tr>
                <td style="padding:8px 0;color:#6b7280;">Adresa:</td>
                <td style="padding:8px 0;color:#111827;">' . htmlspecialchars($orderData['street']) . ', ' . htmlspecialchars($orderData['zip']) . ' ' . htmlspecialchars($orderData['city']) . '</td>
            </tr>
        </table>

        <h3 style="margin:0 0 12px;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Položky</h3>
        <table style="width:100%;border-collapse:collapse;font-family:Arial,sans-serif;margin-bottom:16px;">
            <tr style="border-bottom:2px solid #e5e7eb;">
                <th style="padding:8px 0;font-size:12px;color:#6b7280;text-align:left;">Produkt</th>
                <th style="padding:8px 0;font-size:12px;color:#6b7280;text-align:center;">Mn.</th>
                <th style="padding:8px 0;font-size:12px;color:#6b7280;text-align:right;">Cena</th>
            </tr>
            ' . $itemsHtml . '
            <tr>
                <td colspan="2" style="padding:14px 0;font-size:16px;font-weight:700;color:#111827;">Celkom s DPH</td>
                <td style="padding:14px 0;font-size:18px;font-weight:700;color:#1a5632;text-align:right;">' . formatPrice($total) . '</td>
            </tr>
        </table>' .

        (!empty($orderData['note']) ? '
        <div style="background:#f9fafb;border-left:3px solid #1a5632;padding:12px 16px;border-radius:0 8px 8px 0;margin-bottom:24px;">
            <p style="margin:0 0 4px;font-size:12px;color:#6b7280;">Poznámka:</p>
            <p style="margin:0;font-size:14px;color:#374151;">' . htmlspecialchars($orderData['note']) . '</p>
        </div>' : '') . '

        <a href="mailto:' . htmlspecialchars($orderData['email']) . '" style="display:inline-block;background:#1a5632;color:#fff;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:500;">Kontaktovať zákazníka</a>';

    // ADMIN_NOTIFY_EMAIL je zvycajne ta ista schranka, z ktorej sa maily
    // odosielaju (SMTP_USER) - normalne SMTP odoslanie "sam sebe" by tichuo
    // zlyhalo (anti-loop ochrana mailservera), preto sa zapisuje priamo do
    // schranky cez IMAP (viz appendToInbox()).
    appendToInbox('Nová objednávka ' . $orderNumber . ' – ' . formatPrice($total), $adminBody);
}

/**
 * Posle zakaznikovi mail pri zmene stavu objednavky (napr. "Odoslana" - zasielka
 * je na ceste). Posiela sa len zakaznikovi, nie na ADMIN_NOTIFY_EMAIL - zmenu
 * stavu vykonava sam admin, netreba mu ju pripominat mailom.
 */
function sendOrderStatusEmail(array $order, string $newStatus, string $adminNote = ''): void
{
    $content = [
        'odoslana' => [
            'subject' => 'Objednávka odoslaná',
            'heading' => 'Vaša objednávka je na ceste',
            'intro'   => 'Vaša zásielka bola odoslaná.',
        ],
        'dorucena' => [
            'subject' => 'Objednávka doručená',
            'heading' => 'Vaša objednávka bola doručená',
            'intro'   => 'Ďakujeme za nákup. Veríme, že ste spokojní.',
        ],
        'zrusena' => [
            'subject' => 'Objednávka zrušená',
            'heading' => 'Vaša objednávka bola zrušená',
            'intro'   => 'Vaša objednávka bola zrušená.',
        ],
    ][$newStatus] ?? null;

    if ($content === null) {
        return;
    }

    $orderNumber = $order['order_number'];

    $body = '
        <h2 style="margin:0 0 8px;font-size:20px;color:#111827;">' . htmlspecialchars($content['heading']) . '</h2>
        <p style="margin:0 0 24px;color:#6b7280;font-size:14px;">' . htmlspecialchars($content['intro']) . '</p>

        <div style="background:#f0f7f3;border-radius:8px;padding:16px 20px;margin-bottom:24px;text-align:center;">
            <p style="margin:0 0 4px;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Číslo objednávky</p>
            <p style="margin:0;font-size:24px;font-weight:700;color:#1a5632;">' . htmlspecialchars($orderNumber) . '</p>
        </div>' .

        (trim($adminNote) !== '' ? '
        <div style="background:#f9fafb;border-left:3px solid #1a5632;padding:12px 16px;border-radius:0 8px 8px 0;margin-bottom:24px;">
            <p style="margin:0 0 4px;font-size:12px;color:#6b7280;">Poznámka:</p>
            <p style="margin:0;font-size:14px;color:#374151;">' . htmlspecialchars($adminNote) . '</p>
        </div>' : '') . '

        <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.6;">V prípade otázok nás kontaktujte na <a href="mailto:' . COMPANY_EMAIL . '" style="color:#1a5632;">' . COMPANY_EMAIL . '</a> alebo na tel. č. ' . COMPANY_PHONE . '.</p>';

    sendHtmlEmail($order['email'], 'Objednávka ' . $orderNumber . ' – ' . $content['subject'] . ' – ' . COMPANY_NAME, $body, ADMIN_NOTIFY_EMAIL);
}
