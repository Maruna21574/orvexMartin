<?php
/** Karty prevedeni retazi. Vstup: $modelKeys (kluce z verigaModels()), volitelne $modelsTitle, $modelsLead. */
$allModels = verigaModels();
?>
<section class="section veriga-models-section">
    <div class="container">
        <div class="section__header">
            <span class="section__eyebrow">Prevedenia</span>
            <h2><?= e($modelsTitle ?? 'Tri základné prevedenia') ?></h2>
            <p><?= e($modelsLead ?? 'Výber závisí od zložitosti terénu a hmotnosti stroja.') ?></p>
        </div>
        <div class="vmodels vmodels--<?= count($modelKeys) ?>">
            <?php foreach ($modelKeys as $mk): $m = $allModels[$mk]; ?>
                <article class="vmodel">
                    <div class="vmodel__photo">
                        <span class="vmodel__level"><?= e($m['level']) ?></span>
                        <img src="/assets/images/veriga/<?= e($m['image']) ?>.webp" alt="Snehová reťaz VERIGA <?= e($m['name']) ?> na pneumatike" width="720" height="925" loading="lazy">
                    </div>
                    <div class="vmodel__body">
                        <h3><?= e($m['name']) ?></h3>
                        <p class="vmodel__sub"><?= e($m['sub']) ?></p>
                        <p><?= e($m['text']) ?></p>
                        <ul class="vmodel__list">
                            <?php foreach ($m['bullets'] as $b): ?>
                                <li><?= e($b) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="vmodel__thick">
                            <span>Hrúbky reťaze</span>
                            <div>
                                <?php foreach ($m['thickness'] as $t): ?>
                                    <b><?= e($t) ?></b>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="#dopyt" class="btn btn--outline btn--sm vmodel__cta">Vyžiadať ponuku</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>