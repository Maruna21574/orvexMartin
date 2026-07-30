<?php
$pageTitle = 'Reklamačný poriadok';
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Reklamačný poriadok</h1>
        <nav class="breadcrumb">
            <a href="/">Domov</a>
            <span>/</span>
            <span>Reklamačný poriadok</span>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="legal-content">
            <p class="legal-updated">Platný od: 1. 1. 2024</p>

            <h2>I. Všeobecné ustanovenia</h2>
            <p>Tento reklamačný poriadok upravuje spôsob a podmienky reklamácie vád tovaru zakúpeného prostredníctvom internetového obchodu spoločnosti <?= e(COMPANY_NAME) ?>, so sídlom <?= e(COMPANY_ADDRESS) ?>, <?= e(COMPANY_ZIP) ?> <?= e(COMPANY_CITY) ?>, IČO: <?= e(COMPANY_ICO) ?>.</p>

            <h2>II. Záručná doba</h2>
            <ol>
                <li>Záručná doba na tovar je 24 mesiacov odo dňa prevzatia tovaru kupujúcim, ak nie je pre konkrétny tovar stanovená záručná doba iná.</li>
                <li>Pre podnikateľské subjekty sa záručná doba riadi príslušnými ustanoveniami Obchodného zákonníka.</li>
                <li>Záručná doba začína plynúť dňom prevzatia tovaru kupujúcim.</li>
            </ol>

            <h2>III. Podmienky reklamácie</h2>
            <ol>
                <li>Kupujúci má právo reklamovať tovar, ktorý vykazuje vady brániacie jeho riadnemu užívaniu, alebo nezodpovedá dohodnutým či deklarovaným vlastnostiam.</li>
                <li>Reklamácia sa nevzťahuje na vady spôsobené:
                    <ul>
                        <li>mechanickým poškodením tovaru kupujúcim</li>
                        <li>používaním tovaru v podmienkach nezodpovedajúcich svojou povahou určenému účelu</li>
                        <li>neodbornou montážou, zaobchádzaním alebo obsluhou</li>
                        <li>bežným opotrebením tovaru</li>
                        <li>prírodnými živlami alebo vyššou mocou</li>
                    </ul>
                </li>
            </ol>

            <h2>IV. Postup pri reklamácii</h2>
            <ol>
                <li>Kupujúci uplatní reklamáciu vyplnením <a href="/docs/reklamacny-formular.pdf" target="_blank">reklamačného formulára</a> a jeho zaslaním spolu s reklamovaným tovarom na adresu predávajúceho.</li>
                <li>K reklamovanému tovaru je potrebné priložiť kópiu dokladu o kúpe a popis reklamovanej vady.</li>
                <li>Predávajúci potvrdí prijatie reklamácie vystavením <a href="/docs/protokol-o-prijati-reklamacie.pdf" target="_blank">protokolu o prijatí reklamácie</a>.</li>
                <li>Tovar zasielaný na reklamáciu musí byť zabalený v primeranom obale, aby nedošlo k jeho poškodeniu počas prepravy.</li>
            </ol>

            <h2>V. Lehoty na vybavenie reklamácie</h2>
            <ol>
                <li>Predávajúci rozhodne o reklamácii ihneď, v zložitých prípadoch do 3 pracovných dní.</li>
                <li>Reklamácia vrátane odstránenia vady bude vybavená bez zbytočného odkladu, najneskôr do 30 dní odo dňa uplatnenia reklamácie.</li>
                <li>Po uplynutí lehoty na vybavenie reklamácie má kupujúci právo od zmluvy odstúpiť alebo má právo na výmenu tovaru za nový.</li>
            </ol>

            <h2>VI. Spôsob vybavenia reklamácie</h2>
            <p>Predávajúci vybaví reklamáciu jedným z nasledujúcich spôsobov:</p>
            <ol>
                <li>Odstránením vady tovaru (opravou)</li>
                <li>Výmenou tovaru za nový</li>
                <li>Vrátením kúpnej ceny tovaru</li>
                <li>Poskytnutím primeranej zľavy z ceny tovaru</li>
                <li>Odôvodneným zamietnutím reklamácie</li>
            </ol>

            <h2>VII. Záverečné ustanovenia</h2>
            <p>Tento reklamačný poriadok nadobúda účinnosť dňom jeho zverejnenia. Predávajúci si vyhradzuje právo na jeho zmenu bez predchádzajúceho upozornenia.</p>

            <div class="legal-downloads">
                <h3>Na stiahnutie</h3>
                <a href="/docs/reklamacny-formular.pdf" target="_blank" class="legal-download">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Reklamačný formulár (PDF)
                </a>
                <a href="/docs/protokol-o-prijati-reklamacie.pdf" target="_blank" class="legal-download">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Protokol o prijatí reklamácie (PDF)
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
