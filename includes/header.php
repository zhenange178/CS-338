<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$userID = 0;
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
}
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
            if (($userID >= 100000 and $userID <= 100100) || $userID == 999999) {
        ?>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/initialize.php">Initialize</a></li>
            <li><a href="/products.php">Products</a></li>
            <li style="float:right"><a href="/me.php">My Account</a></li>
            <?php
                if ($userID == 999999) {
            ?>
            <li style="float:right"><a href="/admin_home.php">Admin Home</a></li>
            <?php
                } else {
            ?>
            <li style="float:right"><a href="/cart.php">My Cart</a></li>
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