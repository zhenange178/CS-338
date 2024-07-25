<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
?>

<?php include 'includes/header_home.php'; ?>

    <div>
        <h2>PHP and MySQL Setup and Site Initialization</h2>
        If you haven't already, set up PHP and MySQL, and activate the required extensions. Instructions in README on <a href="https://github.com/zhenange178/CS-338" target="_blank" rel="noopener noreferrer">GitHub</a>.<br/>
        First-time-users: proceed to the Initialization page via the header bar or by clicking <a href="/initialize.php">here</a>.<br/>

        <div class = "divider"></div>

        <h2>Recommended Products</h2>
        <?php include 'includes/product_analysis.php'; ?>

        <div class = "divider"></div>
        
        <h2>View All Tables</h2>
        View all database tables.<br /><br />
        <a href="viewDB_production.php" class="initbutton buttonBlue">Production Database</a>
        <a href="viewDB_sample.php" class="initbutton buttonOrange">Sample Database</a>
        <br/><br/>
    </div>

<?php include 'includes/footer.php'; ?>