<?php
$pageTitle = 'Ochrana osobných údajov';
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Ochrana osobných údajov</h1>
        <nav class="breadcrumb">
            <a href="/">Domov</a>
            <span>/</span>
            <span>Ochrana osobných údajov</span>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="legal-content">
            <p class="legal-updated">Platné od: 1. 1. 2024</p>

            <h2>I. Prevádzkovateľ</h2>
            <p>Prevádzkovateľom osobných údajov v zmysle Nariadenia Európskeho parlamentu a Rady (EÚ) 2016/679 (GDPR) a zákona č. 18/2018 Z. z. o ochrane osobných údajov je:</p>
            <p><strong><?= e(COMPANY_NAME) ?></strong><br>
            <?= e(COMPANY_ADDRESS) ?>, <?= e(COMPANY_ZIP) ?> <?= e(COMPANY_CITY) ?><br>
            IČO: <?= e(COMPANY_ICO) ?><br>
            E-mail: <?= e(COMPANY_EMAIL) ?><br>
            Telefón: <?= e(COMPANY_PHONE) ?></p>

            <h2>II. Aké osobné údaje spracúvame</h2>
            <p>Pri nákupe a komunikácii prostredníctvom nášho e-shopu spracúvame nasledovné osobné údaje:</p>
            <ul>
                <li>Meno a priezvisko</li>
                <li>Adresa (ulica, mesto, PSČ)</li>
                <li>E-mailová adresa</li>
                <li>Telefónne číslo</li>
                <li>Obchodné meno, IČO, DIČ, IČ DPH (v prípade podnikateľov)</li>
            </ul>

            <h2>III. Účel spracúvania osobných údajov</h2>
            <p>Vaše osobné údaje spracúvame na nasledovné účely:</p>
            <ol>
                <li><strong>Vybavenie objednávky</strong> – spracovanie, expedícia a doručenie objednaného tovaru (právny základ: plnenie zmluvy).</li>
                <li><strong>Účtovné a daňové povinnosti</strong> – vystavenie faktúr a vedenie účtovníctva (právny základ: zákonná povinnosť).</li>
                <li><strong>Komunikácia</strong> – odpovede na vaše otázky a požiadavky prostredníctvom kontaktného formulára (právny základ: oprávnený záujem).</li>
                <li><strong>Reklamácie a záručný servis</strong> – vybavenie reklamácií (právny základ: zákonná povinnosť).</li>
            </ol>

            <h2>IV. Doba uchovávania údajov</h2>
            <ol>
                <li>Údaje súvisiace s objednávkou uchovávame po dobu 10 rokov (zákonná povinnosť – účtovné a daňové predpisy).</li>
                <li>Údaje z kontaktných formulárov uchovávame po dobu nevyhnutnú na vybavenie požiadavky, maximálne 1 rok.</li>
                <li>Údaje súvisiace s reklamáciami uchovávame po dobu záručnej doby a ďalších 12 mesiacov.</li>
            </ol>

            <h2>V. Príjemcovia osobných údajov</h2>
            <p>Vaše osobné údaje môžu byť poskytnuté nasledovným tretím stranám:</p>
            <ul>
                <li>Prepravným spoločnostiam za účelom doručenia tovaru</li>
                <li>Účtovnej spoločnosti za účelom spracovania účtovníctva</li>
                <li>Štátnym orgánom v zákonom stanovených prípadoch</li>
            </ul>
            <p>Vaše údaje neposúvame do tretích krajín mimo EÚ.</p>

            <h2>VI. Vaše práva</h2>
            <p>Ako dotknutá osoba máte právo:</p>
            <ul>
                <li><strong>Právo na prístup</strong> – požiadať o informáciu, aké údaje o vás spracúvame.</li>
                <li><strong>Právo na opravu</strong> – požiadať o opravu nesprávnych alebo neúplných údajov.</li>
                <li><strong>Právo na vymazanie</strong> – požiadať o vymazanie údajov, ak pominul účel ich spracúvania.</li>
                <li><strong>Právo na obmedzenie spracúvania</strong> – požiadať o obmedzenie spracúvania za určitých podmienok.</li>
                <li><strong>Právo na prenosnosť údajov</strong> – získať údaje v štruktúrovanom, bežne používanom formáte.</li>
                <li><strong>Právo namietať</strong> – namietať voči spracúvaniu na základe oprávneného záujmu.</li>
                <li><strong>Právo podať sťažnosť</strong> – podať sťažnosť na Úrade na ochranu osobných údajov SR.</li>
            </ul>

            <h2>VII. Cookies</h2>
            <p>Naša internetová stránka používa cookies nevyhnutné pre správne fungovanie stránky (session cookies pre košík a prihlásenie). Tieto cookies sú technicky nevyhnutné a nevyžadujú súhlas.</p>

            <h2>VIII. Kontakt</h2>
            <p>V prípade otázok ohľadom spracovania osobných údajov nás kontaktujte na e-mailovej adrese <a href="mailto:<?= e(COMPANY_EMAIL) ?>"><?= e(COMPANY_EMAIL) ?></a> alebo písomne na adrese sídla spoločnosti.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
