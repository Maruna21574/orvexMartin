<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'ORVEX Martin') ?> | <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($pageDescription ?? 'ORVEX MT s.r.o. - Predaj lesnej kolesovej techniky, náhradných dielov, snehových reťazí a hydraulických čerpadiel od roku 1991.') ?>">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body>
    <header class="header" id="siteHeader">
        <div class="header__top">
            <div class="container">
                <div class="header__top-inner">
                    <span class="header__contact">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <?= e(COMPANY_PHONE) ?>
                    </span>
                    <span class="header__contact">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <?= e(COMPANY_EMAIL) ?>
                    </span>
                </div>
            </div>
        </div>
        <nav class="header__nav">
            <div class="container">
                <div class="header__nav-inner">
                    <a href="/" class="header__logo">
                        <img src="/assets/images/orvex_logo_hl.png" alt="<?= e(COMPANY_NAME) ?>" class="header__logo-img">
                    </a>

                    <ul class="header__menu" id="mainMenu">
                        <li><a href="/" class="<?= isActivePage('index') ?>">Domov</a></li>
                        <li><a href="/o-nas" class="<?= isActivePage('o-nas') ?>">O nás</a></li>
                        <li><a href="/produkty" class="<?= isActivePage('produkty') ?>">Produkty</a></li>
                        <li><a href="/kontakt" class="<?= isActivePage('kontakt') ?>">Kontakt</a></li>
                        <li><a href="/kosik" class="<?= isActivePage('kosik') ?>">Košík</a></li>
                    </ul>

                    <div class="header__actions">
                        <div class="header__search-wrap" id="headerSearchWrap">
                            <form class="header__search" action="/produkty" method="get" id="headerSearchForm">
                                <input type="text" name="hladaj" placeholder="Názov alebo katalógové číslo..." class="header__search-input" id="headerSearchInput" autocomplete="off">
                                <button type="submit" class="header__search-btn" aria-label="Hľadať">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                </button>
                            </form>
                            <div class="search-dropdown" id="searchDropdown"></div>
                        </div>
                        <button class="header__search-toggle" id="searchToggle" aria-label="Hľadať">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </button>
                        <a href="/kosik" class="header__cart" aria-label="Košík">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            <span class="header__cart-count" id="cartCount"><?= getCartCount() ?></span>
                        </a>
                        <button class="header__hamburger" id="hamburgerBtn" aria-label="Menu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="main">
