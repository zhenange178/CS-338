<?php include 'includes/header.php'; ?>
<?php
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";
if (isset($_GET['data'])) {
    if ($_GET['data'] === 'sample'){
        $dbname = "sampledatabase";
    }
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Place an Order</h1>";

?>