    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer__top">
                <div class="footer__brand">
                    <div class="footer__logo">
                        <img src="/assets/images/orvex_logo_hl.png" alt="<?= e(COMPANY_NAME) ?>" class="footer__logo-img">
                    </div>
                    <p>Predaj lesnej kolesovej techniky, náhradných dielov a príslušenstva od roku 1991.</p>
                </div>
                <div class="footer__contacts">
                    <a href="tel:+421434135968" class="footer__contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <?= e(COMPANY_PHONE) ?>
                    </a>
                    <a href="mailto:<?= e(COMPANY_EMAIL) ?>" class="footer__contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <?= e(COMPANY_EMAIL) ?>
                    </a>
                    <span class="footer__contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?= e(COMPANY_ADDRESS) ?>, <?= e(COMPANY_ZIP) ?> <?= e(COMPANY_CITY) ?>
                    </span>
                </div>
            </div>

            <div class="footer__mid">
                <div class="footer__col">
                    <h4>Navigácia</h4>
                    <ul>
                        <li><a href="/">Domov</a></li>
                        <li><a href="/o-nas">O nás</a></li>
                        <li><a href="/produkty">Produkty</a></li>
                        <li><a href="/kontakt">Kontakt</a></li>
                    </ul>
                </div>
                <div class="footer__col">
                    <h4>Kategórie</h4>
                    <ul>
                        <?php foreach (getCategories() as $footerCategory): ?>
                            <li><a href="/produkty?kategoria=<?= e($footerCategory['id']) ?>"><?= e($footerCategory['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer__col">
                    <h4>Informácie k nákupu</h4>
                    <ul>
                        <li><a href="/obchodne-podmienky">Obchodné podmienky</a></li>
                        <li><a href="/reklamacny-poriadok">Reklamačný poriadok</a></li>
                        <li><a href="/ochrana-osobnych-udajov">Ochrana osobných údajov</a></li>
                        <li>
                            <button type="button" class="footer__cookie-link" id="cookieSettingsFooterLink">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="8.5" cy="10.5" r="1"/><circle cx="12" cy="15" r="1"/><circle cx="15.5" cy="9" r="1"/></svg>
                                Nastavenia cookies
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="footer__col">
                    <h4>Na stiahnutie</h4>
                    <ul>
                        <li>
                            <a href="/docs/formular-odstupenie-od-zmluvy.pdf" target="_blank">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Odstúpenie od zmluvy
                            </a>
                        </li>
                        <li>
                            <a href="/docs/reklamacny-formular.pdf" target="_blank">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Reklamačný formulár
                            </a>
                        </li>
                        <li>
                            <a href="/docs/protokol-o-prijati-reklamacie.pdf" target="_blank">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Protokol o reklamácii
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer__bottom">
                <p>&copy; <?= date('Y') ?> <?= e(COMPANY_NAME) ?>. Všetky práva vyhradené. IČO: <?= e(COMPANY_ICO) ?></p>
            </div>
        </div>
    </footer>

    <div class="toast" id="toast"></div>

    <button class="back-to-top" id="backToTop" aria-label="Späť hore">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg>
    </button>

    <div class="cookie-banner" id="cookieBanner" hidden>
        <div class="cookie-banner__inner">
            <div class="cookie-banner__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="8.5" cy="10.5" r="1"/><circle cx="12" cy="15" r="1"/><circle cx="15.5" cy="9" r="1"/></svg>
            </div>
            <div class="cookie-banner__text">
                <h4>Používame cookies</h4>
                <p>Nevyhnutné cookies potrebujeme pre fungovanie stránky (napr. košík). So súhlasom môžeme použiť aj analytické a marketingové cookies. Viac v <a href="/ochrana-osobnych-udajov">Ochrane osobných údajov</a>.</p>
            </div>
            <div class="cookie-banner__actions">
                <button type="button" class="btn btn--outline btn--sm" id="cookieSettingsBtn">Nastavenia</button>
                <button type="button" class="btn btn--outline btn--sm" id="cookieDeclineBtn">Odmietnuť</button>
                <button type="button" class="btn btn--primary btn--sm" id="cookieAcceptBtn">Prijať všetko</button>
            </div>
        </div>
    </div>

    <div class="cookie-modal-overlay" id="cookieModalOverlay" hidden>
        <div class="cookie-modal" role="dialog" aria-modal="true" aria-labelledby="cookieModalTitle">
            <div class="cookie-modal__header">
                <h3 id="cookieModalTitle">Nastavenia cookies</h3>
                <button type="button" class="cookie-modal__close" id="cookieModalClose" aria-label="Zavrieť">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="cookie-modal__body">
                <p>Vyberte si, ktoré cookies chcete povoliť. Nevyhnutné cookies sú vždy aktívne, keďže bez nich stránka nefunguje správne.</p>
                <div class="cookie-category">
                    <div class="cookie-category__head">
                        <span>Nevyhnutné</span>
                        <span class="cookie-category__locked">Vždy aktívne</span>
                    </div>
                    <p>Potrebné pre základné fungovanie stránky - napríklad zapamätanie obsahu košíka. Nedajú sa vypnúť.</p>
                </div>
                <div class="cookie-category">
                    <div class="cookie-category__head">
                        <span>Analytické</span>
                        <label class="cookie-toggle">
                            <input type="checkbox" id="cookieAnalyticsToggle">
                            <span class="cookie-toggle__slider"></span>
                        </label>
                    </div>
                    <p>Pomáhajú nám pochopiť, ako návštevníci stránku používajú, aby sme ju mohli zlepšovať.</p>
                </div>
                <div class="cookie-category">
                    <div class="cookie-category__head">
                        <span>Marketingové</span>
                        <label class="cookie-toggle">
                            <input type="checkbox" id="cookieMarketingToggle">
                            <span class="cookie-toggle__slider"></span>
                        </label>
                    </div>
                    <p>Používajú sa na zobrazovanie relevantnej reklamy na tejto a iných stránkach.</p>
                </div>
            </div>
            <div class="cookie-modal__footer">
                <button type="button" class="btn btn--outline btn--sm" id="cookieDeclineAllBtn">Odmietnuť všetko</button>
                <button type="button" class="btn btn--primary btn--sm" id="cookieSaveBtn">Uložiť nastavenia</button>
            </div>
        </div>
    </div>

    <button class="cookie-fab" id="cookieFab" aria-label="Nastavenia cookies" hidden>
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="8.5" cy="10.5" r="1"/><circle cx="12" cy="15" r="1"/><circle cx="15.5" cy="9" r="1"/></svg>
    </button>

    <script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?>"></script>
</body>
</html>
