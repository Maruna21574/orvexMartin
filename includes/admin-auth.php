<?php

function requireAdminLogin(): void
{
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: /admin/login');
        exit;
    }
}
