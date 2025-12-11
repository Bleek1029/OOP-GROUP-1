<?php
session_start();

// Simple helper
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Read cookie list
$accounts = [];
if (isset($_COOKIE['remembered_accounts'])) {
    $decoded = json_decode($_COOKIE['remembered_accounts'], true);
    if (is_array($decoded)) {
        $accounts = $decoded;
    }
}

// Selecting an account to continue
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim((string)$_POST['email']);
    if ($email === '') {
        redirect('account_switcher.php');
    }

    // Option A: Pre-fill login page with selected email
    redirect('login.php?from=switcher&email=' . urlencode($email));
}

// Fallback
redirect('account_switcher.php');