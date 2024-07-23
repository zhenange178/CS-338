<?php include 'includes/header.php'; ?>
<?php
// include
require 'includes/ViewDB.php';

function showTable($conn, $tableName){
    $tableDisplay = new ViewDB($conn);
    $tableDisplay->displayTable("SELECT * FROM " . $tableName);
}

// Database connection parameters
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$allTables = ['products', 'productCategories', 'productColors', 'productPrices', 'customers', 'memberships', 'orders', 'returnedOrders', 'orderDetails', 'reviews', 'promoCodes'];

echo "<h1>Production Database</h1>";

foreach ($allTables as $table){
    echo"<br/>" . $table . "<br/>";
    showTable($conn, $table);
}

echo "<br />";

$conn->close();
?>

<?php include 'includes/footer.php'; ?>