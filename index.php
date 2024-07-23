<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
?>

<?php include 'includes/header.php'; ?>

    <div>
        <h2>About</h2>
        H&M Promo Site

        <h2>PHP and MySQL Setup</h2>
        If you haven't already, set up PHP and MySQL, and activate the required extensions. Instructions in README on <a href="https://github.com/zhenange178/CS-338" target="_blank" rel="noopener noreferrer">GitHub</a>.<br/>

        <h2>Initialization</h2>
        First-time-users: proceed to the Initialization page via the header bar or by clicking <a href="/initialize.php">here</a>.<br/>

        <h2>Features</h2>
        Once initialized, check out the site's features via the header bar.<br />

        <h2>View All Tables</h2>
        View all database tables.<br /><br />
        <a href="viewDB_production.php" class="initbutton buttonBlue">Production Database</a>
        <a href="viewDB_sample.php" class="initbutton buttonOrange">Sample Database</a>
        <br/><br/>
    </div>

<?php include 'includes/footer.php'; ?>