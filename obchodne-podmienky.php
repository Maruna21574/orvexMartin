<?php
$pageTitle = 'Obchodné podmienky';
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Obchodné podmienky</h1>
        <nav class="breadcrumb">
            <a href="/">Domov</a>
            <span>/</span>
            <span>Obchodné podmienky</span>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="legal-content">
            <p class="legal-updated">Platné od: 1. 1. 2024</p>

            <h2>I. Základné ustanovenia</h2>
            <p>Tieto všeobecné obchodné podmienky (ďalej len „VOP") upravujú práva a povinnosti zmluvných strán vyplývajúce z kúpnej zmluvy uzatvorenej medzi predávajúcim a kupujúcim, ktorej predmetom je kúpa a predaj tovaru prostredníctvom internetového obchodu predávajúceho.</p>
            <p><strong>Predávajúci:</strong><br>
            <?= e(COMPANY_NAME) ?><br>
            <?= e(COMPANY_ADDRESS) ?>, <?= e(COMPANY_ZIP) ?> <?= e(COMPANY_CITY) ?><br>
            IČO: <?= e(COMPANY_ICO) ?>, DIČ: <?= e(COMPANY_DIC) ?>, IČ DPH: <?= e(COMPANY_IC_DPH) ?><br>
            Zapísaná v Obchodnom registri Okresného súdu Žilina, Oddiel: Sro, vložka číslo: 50737/L</p>

            <h2>II. Objednávka a uzatvorenie kúpnej zmluvy</h2>
            <ol>
                <li>Kupujúci si objednáva tovar prostredníctvom vyplnenia objednávkového formulára na internetovej stránke predávajúceho.</li>
                <li>Odoslaním objednávky kupujúci potvrdzuje, že sa oboznámil s týmito VOP a že s nimi súhlasí.</li>
                <li>Kúpna zmluva je uzatvorená záväzným akceptovaním objednávky kupujúceho predávajúcim, a to vo forme potvrdenia objednávky e-mailom.</li>
                <li>Predávajúci si vyhradzuje právo odmietnuť objednávku alebo jej časť v prípade, že tovar nie je skladom alebo sa výrazne zmenila jeho cena.</li>
            </ol>

            <h2>III. Ceny a platobné podmienky</h2>
            <ol>
                <li>Ceny uvedené na internetovej stránke predávajúceho sú uvádzané vrátane DPH aj bez DPH.</li>
                <li>Predávajúci si vyhradzuje právo na zmenu cien. Cena tovaru je platná v čase objednania.</li>
                <li>Kupujúci môže za tovar zaplatiť:
                    <ul>
                        <li>Bankovým prevodom na účet predávajúceho</li>
                        <li>Dobierkou pri prevzatí tovaru</li>
                        <li>V hotovosti pri osobnom odbere</li>
                    </ul>
                </li>
            </ol>

            <h2>IV. Dodacie podmienky</h2>
            <ol>
                <li>Predávajúci sa zaväzuje dodať tovar kupujúcemu v čo najkratšom čase, zvyčajne do 3–7 pracovných dní od potvrdenia objednávky.</li>
                <li>V prípade tovaru, ktorý nie je na sklade, bude kupujúci informovaný o predpokladanom termíne dodania.</li>
                <li>Cena dopravy bude stanovená individuálne podľa hmotnosti, rozmerov a adresy doručenia.</li>
                <li>Tovar je možné prevziať aj osobne na adrese predávajúceho po predchádzajúcej dohode.</li>
            </ol>

            <h2>V. Odstúpenie od zmluvy</h2>
            <ol>
                <li>Kupujúci – spotrebiteľ má právo odstúpiť od kúpnej zmluvy bez udania dôvodu v lehote 14 dní odo dňa prevzatia tovaru.</li>
                <li>Právo na odstúpenie od zmluvy je kupujúci povinný uplatniť písomnou formou alebo prostredníctvom <a href="/docs/formular-odstupenie-od-zmluvy.pdf" target="_blank">formulára na odstúpenie od zmluvy</a>.</li>
                <li>Tovar musí byť vrátený nepoškodený, nepoužitý, v pôvodnom obale a s dokladom o kúpe.</li>
                <li>Predávajúci vráti kupujúcemu zaplatenú sumu za tovar do 14 dní odo dňa doručenia vráteného tovaru.</li>
            </ol>

            <h2>VI. Záručné podmienky a reklamácie</h2>
            <ol>
                <li>Na všetok predávaný tovar je poskytovaná záručná doba v súlade s platnými právnymi predpismi SR, t. j. 24 mesiacov pre spotrebiteľov.</li>
                <li>Reklamáciu je kupujúci povinný uplatniť u predávajúceho bez zbytočného odkladu po zistení vady, a to prostredníctvom <a href="/docs/reklamacny-formular.pdf" target="_blank">reklamačného formulára</a>.</li>
                <li>Podrobné podmienky reklamácie sú uvedené v <a href="/reklamacny-poriadok">Reklamačnom poriadku</a>.</li>
            </ol>

            <h2>VII. Záverečné ustanovenia</h2>
            <ol>
                <li>Tieto VOP nadobúdajú účinnosť dňom ich zverejnenia na internetovej stránke predávajúceho.</li>
                <li>Predávajúci si vyhradzuje právo na zmenu týchto VOP. Zmena VOP je účinná dňom ich zverejnenia.</li>
                <li>Na vzťahy neupravené týmito VOP sa vzťahujú príslušné ustanovenia Občianskeho zákonníka, Zákona o ochrane spotrebiteľa a Zákona o elektronickom obchode.</li>
            </ol>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
