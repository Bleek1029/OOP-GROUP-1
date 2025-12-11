<?php
// Start the session so we can access it.
session_start();

// Unset all session variables to clear the user's data.
session_unset();

// Destroy the session completely.
session_destroy();

// Redirect the user to the registration page using a relative path to avoid server path issues.
header("Location: register.php");
exit;
?>