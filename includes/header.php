<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>H&M Project</title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
</head>
<body>
    <header class="header-nav">
        <?php 
            if ($role == 'user' || $role == 'admin') {
        ?>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/initialize.php">Initialize</a></li>
            <li><a href="/products.php">Products</a></li>
            <li style="float:right"><a href="/me.php">My Account</a></li>
            <?php
                if ($role == 'admin') {
            ?>
            <li style="float:right"><a href="/admin_home.php">Admin Home</a></li>
            <?php
                }
            ?>
        </ul>
        <?php
            }
        ?>
    </header>
    </div>
    <div class="content">