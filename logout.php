<?php
/**
 * SBA Screening - Secure Admin Logout
 * Destroys administrative sessions and redirects to the login portal.
 */

session_start();

// 1. Clear all session variables
$_SESSION = array();

// 2. If it's desired to kill the session, also delete the session cookie.
// This completely resets the session on the client side.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session on the server
session_destroy();

// 4. Redirect to the admin login page
header('location:admin.php');

// 5. Ensure no further code is executed
exit();
?>