<?php
require_once __DIR__ . '/../config.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: /admin');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verifyCsrfToken($token)) {
        $error = 'Neplatný bezpečnostný token. Skúste to znova.';
    } elseif ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: /admin');
        exit;
    } else {
        $error = 'Nesprávne prihlasovacie údaje.';
    }
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prihlásenie | Admin | <?= e(SITE_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?= filemtime(__DIR__ . '/../assets/css/admin.css') ?>">
</head>
<body>
    <div class="login-card">
        <img src="/assets/images/orvex_logo_hl.png" alt="<?= e(COMPANY_NAME) ?>" class="login-card__logo">
        <h1>Prihlásenie do administrácie</h1>

        <?php if ($error !== ''): ?>
            <div class="alert alert--error">
                <p><?= e($error) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <div class="form-group">
                <label for="username">Používateľské meno</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Heslo</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn--primary btn--block" style="margin-top:20px;">Prihlásiť sa</button>
        </form>
    </div>
</body>
</html>
