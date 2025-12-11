<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $emailToRemove = $_POST['email'];

    if (isset($_COOKIE['remembered_accounts'])) {
        $accounts = json_decode($_COOKIE['remembered_accounts'], true);

        // Filter out the account to be removed
        $updatedAccounts = array_filter($accounts, function($account) use ($emailToRemove) {
            return strcasecmp($account['email'], $emailToRemove) !== 0;
        });

        // Re-index the array to prevent it from becoming an object if it's sparse
        $updatedAccounts = array_values($updatedAccounts);

        // Save the updated array back to the cookie
        setcookie('remembered_accounts', json_encode($updatedAccounts), time() + (86400 * 365), "/");
    }
}

// Redirect back to the account switcher, or to the login page if no accounts are left
header("Location: account_switcher.php");
exit;
?>  