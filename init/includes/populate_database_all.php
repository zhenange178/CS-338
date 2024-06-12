<?php
// includes
include 'DatabasePopulator.php';

// Database credentials
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$populator = new DatabasePopulator();

echo "<b>Populating Database...</b><br />";
$startTime = microtime(true);
echo "Writing data to tables...<br />";

$dataPopulated = true;

$productsData = $populator->readJson('hm_product_list.json');
$mockData = $populator->readJson('mock_data.json');

$populator->importProducts($mysqli, $productsData);
$populator->importCustomers($mysqli, $mockData);
$populator->importMemberships($mysqli, $mockData);
$populator->importOrders($mysqli, $mockData);
$populator->importReviews($mysqli, $mockData);

$endTime = microtime(true);
$executionTime = $endTime - $startTime;
echo "Data written to database tables in " . number_format($executionTime, 2) . " seconds.<br /><br />";

// Close the connection
$mysqli->close();
?>