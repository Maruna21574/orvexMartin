<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/admin-auth.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> | <?= e(SITE_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../assets/css/style.css') ?>">
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin.css') ?>">
</head>
<body>
    <div class="admin-topbar">
        <div class="container admin-topbar__inner">
            <a href="/admin" class="admin-topbar__logo">
                <img src="/assets/images/orvex_logo_hl.png" alt="<?= e(COMPANY_NAME) ?>">
                <span>Admin</span>
            </a>
            <a href="/admin/logout" class="admin-topbar__logout">Odhlásiť sa</a>
        </div>
    </div>

    <main class="admin-main">
        <div class="container">
