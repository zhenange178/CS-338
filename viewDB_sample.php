<?php
// include
require 'ViewDB.php';

function showTable($conn, $tableName){
    $tableDisplay = new ViewDB($conn, $tableName);
    $tableDisplay->displayTable();
}

// Database connection parameters
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "sampledatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$allTables = ['products', 'productCategories', 'productColors', 'productPrices', 'customers', 'memberships', 'orders', 'reviews', 'promoCodes'];

foreach ($allTables as $table){
    echo"<br/>" . $table . "<br/>";
    showTable($conn, $table);
}

echo "<br />";

$conn->close();
?>