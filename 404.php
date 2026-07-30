<?php
http_response_code(404);
$pageTitle = 'Stránka nenájdená';
require_once 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="error-page">
            <div class="error-page__code">404</div>
            <h1>Stránka nenájdená</h1>
            <p>Ľutujeme, ale stránka ktorú hľadáte neexistuje alebo bola presunutá.</p>
            <div class="error-page__actions">
                <a href="/" class="btn btn--primary btn--lg">Späť na hlavnú stránku</a>
                <a href="/produkty" class="btn btn--outline btn--lg">Zobraziť produkty</a>
            </div>
            <div class="error-page__help">
                <p>Skúste tiež:</p>
                <ul>
                    <li><a href="/produkty">Prehľadať produkty</a></li>
                    <li><a href="/kontakt">Kontaktovať nás</a></li>
                    <li><a href="/o-nas">O spoločnosti</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
