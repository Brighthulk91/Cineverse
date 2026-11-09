<?php
session_start();
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php');

if(isset($_GET['did'])) {
    $did = intval($_GET['did']);
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $did);
    $stmt->execute();
}

header('location:users.php');
exit();
