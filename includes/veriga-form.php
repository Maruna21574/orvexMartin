<?php
/** Dopytovy formular na snehove retaze VERIGA. Volitelne $veriga_preselect = kluc typu (osobne, suv, ...). */

// Jednoduche ikony typov (stroke, 24x24)
$vformIcons = [
    'osobne' => 'M3 14l2-5a2 2 0 0 1 1.9-1.3h10.2A2 2 0 0 1 19 9l2 5v4h-2.5M3 14v4h2.5M3 14h18M7.5 18a1.5 1.5 0 1 0 0-.01M16.5 18a1.5 1.5 0 1 0 0-.01',
    'suv' => 'M2 15l2-6a2 2 0 0 1 1.9-1.4h9a2 2 0 0 1 1.6.8L19 11l3 1.5V17h-2M2 15v2h2M2 15h18M7 17.5a2 2 0 1 0 0-.01M16 17.5a2 2 0 1 0 0-.01',
    'nakladne' => 'M1 6h13v10H1zM14 10h4l3 3v3h-7M5.5 19a1.5 1.5 0 1 0 0-.01M17.5 19a1.5 1.5 0 1 0 0-.01',
    'pracovne' => 'M3 17h9M3 13h8V8h4l3 5v4M6 17a2 2 0 1 0 0-.01M17 18a1 1 0 1 0 0-.01',
    'lesne' => 'M12 3l5 7h-3l4 6H6l4-6H7zM12 16v5',
    'male' => 'M5 17a2 2 0 1 0 0-.01M17 17a2 2 0 1 0 0-.01M7 17h8M4 13l2-5h6l3 5h3M10 8V5h3',
];
?>
<section class="vform-section" id="dopyt">
    <div class="container">
        <div class="vform-layout">
            <aside class="vform-aside">
                <img src="/assets/images/veriga/veriga-logo.png" alt="VERIGA since 1922" class="vform-aside__logo" width="170" height="39" loading="lazy">
                <h2>Dopyt na snehové reťaze</h2>
                <p>Povedzte nám, aké reťaze potrebujete. Cenovú ponuku vám zašleme e-mailom.</p>
                <ul class="vform-aside__perks">
                    <li><span>1</span> Vyberiete typ reťazí</li>
                    <li><span>2</span> Napíšete pneumatiky a stroj</li>
                    <li><span>3</span> Dostanete cenovú ponuku</li>
                </ul>
                <div class="vform-aside__contact">
                    <p>Radšej telefonicky alebo e-mailom?</p>
                    <a href="tel:+421905500950">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        0905 500 950
                    </a>
                    <a href="mailto:<?= e(COMPANY_EMAIL) ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <?= e(COMPANY_EMAIL) ?>
                    </a>
                </div>
            </aside>

            <form class="cform vform" id="verigaForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                <?= renderAntiSpamFields() ?>

                <div class="vform-step">
                    <h3><span class="vform-step__num">1</span> Čo potrebujete <small>*</small></h3>
                    <fieldset class="vform-types">
                        <legend class="sr-only">Typ reťazí</legend>
                        <?php foreach (verigaTypes() as $key => $vt): ?>
                            <label class="vform-type">
                                <input type="radio" name="type" value="<?= e($key) ?>" required<?= ($veriga_preselect ?? '') === $key ? ' checked' : '' ?>>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?= e($vformIcons[$key] ?? '') ?>"/></svg>
                                <span><?= e($vt['label']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>

                <div class="vform-step">
                    <h3><span class="vform-step__num">2</span> Pneumatiky a stroj</h3>
                    <div class="cform__grid">
                        <div class="form-group">
                            <label for="vf_tire">Označenie pneumatík <span class="required">*</span></label>
                            <input type="text" id="vf_tire" name="tire" required maxlength="60" placeholder="napr. 16.9-30 alebo 205/55 R16">
                        </div>
                        <div class="form-group">
                            <label for="vf_machine">Označenie stroja / vozidla <span class="required">*</span></label>
                            <input type="text" id="vf_machine" name="machine" required maxlength="120" placeholder="napr. John Deere 5115 R, LKT 81">
                        </div>
                        <div class="form-group">
                            <label for="vf_quantity">Počet párov</label>
                            <input type="number" id="vf_quantity" name="quantity" min="1" max="50" value="1">
                        </div>
                    </div>
                </div>

                <div class="vform-step">
                    <h3><span class="vform-step__num">3</span> Kontakt na vás</h3>
                    <div class="cform__grid">
                        <div class="form-group">
                            <label for="vf_name">Meno a priezvisko <span class="required">*</span></label>
                            <input type="text" id="vf_name" name="name" required maxlength="100" placeholder="Ján Novák">
                        </div>
                        <div class="form-group">
                            <label for="vf_email">E-mail <span class="required">*</span></label>
                            <input type="email" id="vf_email" name="email" required maxlength="150" placeholder="jan@example.sk">
                        </div>
                        <div class="form-group">
                            <label for="vf_phone">Telefón</label>
                            <input type="tel" id="vf_phone" name="phone" maxlength="40" placeholder="+421 9XX XXX XXX">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vf_note">Poznámka</label>
                        <textarea id="vf_note" name="note" rows="3" maxlength="1500" placeholder="Typ terénu, požiadavky na hrúbku reťaze, termín…"></textarea>
                    </div>
                </div>

                <label class="vform-consent">
                    <input type="checkbox" name="consent" value="1" required>
                    <span>Súhlasím so spracovaním osobných údajov na účely vybavenia dopytu. <a href="/ochrana-osobnych-udajov" target="_blank" rel="noopener">Ochrana osobných údajov</a></span>
                </label>
                <button type="submit" class="btn btn--primary btn--lg vform-submit" id="verigaSubmitBtn">
                    Odoslať dopyt
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
                <p class="vform-note">Povinné polia sú označené <span class="required">*</span>. Cenovú ponuku vám zašleme e-mailom.</p>
            </form>
        </div>
    </div>
</section>
