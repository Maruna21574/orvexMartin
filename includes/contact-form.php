<section class="section <?= ($contactFormAlt ?? false) ? 'section--alt' : '' ?>">
    <div class="container">
        <div class="cform-layout">
            <div class="cform-info">
                <h2><?= $contactFormTitle ?? 'Napíšte nám' ?></h2>
                <p><?= $contactFormDesc ?? 'Máte otázku alebo potrebujete poradiť? Vyplňte formulár a ozveme sa vám čo najskôr.' ?></p>
                <ul class="cform-perks">
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Odpovieme do 24 hodín</span>
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Odborné poradenstvo zdarma</span>
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Individuálny prístup</span>
                    </li>
                </ul>
                <div class="cform-contact-alt">
                    <p>Alebo nás kontaktujte priamo:</p>
                    <a href="tel:+421905500950" class="cform-contact-alt__link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        0905 500 950
                    </a>
                    <a href="mailto:orvex@orvex.sk" class="cform-contact-alt__link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        orvex@orvex.sk
                    </a>
                </div>
            </div>
            <form class="cform" id="contactForm">
                <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                <div class="cform__grid">
                    <div class="form-group">
                        <label for="cf_name">Meno a priezvisko <span class="required">*</span></label>
                        <input type="text" id="cf_name" name="name" required placeholder="Ján Novák">
                    </div>
                    <div class="form-group">
                        <label for="cf_email">E-mail <span class="required">*</span></label>
                        <input type="email" id="cf_email" name="email" required placeholder="jan@example.sk">
                    </div>
                    <div class="form-group">
                        <label for="cf_phone">Telefón</label>
                        <input type="tel" id="cf_phone" name="phone" placeholder="+421 9XX XXX XXX">
                    </div>
                    <div class="form-group">
                        <label for="cf_subject">Predmet</label>
                        <select id="cf_subject" name="subject">
                            <option value="">-- Vyberte --</option>
                            <option value="dopyt">Dopyt na produkt</option>
                            <option value="cenova-ponuka">Cenová ponuka</option>
                            <option value="nahradne-diely">Náhradné diely</option>
                            <option value="reklamacia">Reklamácia</option>
                            <option value="ine">Iné</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cf_message">Správa <span class="required">*</span></label>
                    <textarea id="cf_message" name="message" rows="5" required placeholder="Napíšte vašu správu..."></textarea>
                </div>
                <div class="cform__footer">
                    <button type="submit" class="btn btn--primary btn--lg" id="contactSubmitBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Odoslať správu
                    </button>
                    <p class="cform__note">Povinné polia sú označené <span class="required">*</span></p>
                </div>
            </form>
        </div>
    </div>
</section>
