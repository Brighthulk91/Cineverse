<?php
// Database Configuration
$host = "localhost";
$user = "root";
$pass = "";
$db   = "cinema_db";

// Enable mysqli exception reporting for better security and debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $con = mysqli_connect($host, $user, $pass, $db);
    
    // Set charset to support modern character encoding (emojis, special symbols)
    mysqli_set_charset($con, "utf8mb4");
    
} catch (Exception $e) {
    // Log the error internally and show a user-friendly message
    // This prevents hackers from seeing your database structure in error logs
    error_log($e->getMessage());
    exit('<div style="font-family:sans-serif; text-align:center; padding:50px;">
            <h2 style="color:#f87171;">System Maintenance</h2>
            <p style="color:#64748b;">We are currently unable to connect to the core. Please try again later.</p>
          </div>');
}
?>