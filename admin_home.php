<?php include 'includes/header.php'; ?>
<?php
// include
require 'includes/ViewDB.php';
// Database credentials
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<h1>Admin Center — Production</h1>
For sample database admin center, click <a href="admin_home_sample.php">here</a>.<br/>
<h2></h2>
<?php include 'includes/footer.php'; ?>