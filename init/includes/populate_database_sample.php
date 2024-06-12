<?php
// includes
include 'DatabasePopulator.php';

ob_implicit_flush(1);
while (ob_get_level()) {
    ob_end_flush();
}

set_time_limit(120);

// Database credentials
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "sampledatabase";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$populator = new DatabasePopulator();

echo "<b>Populating Database...</b><br />";
$startTime = microtime(true);
echo "Writing data to tables...<br />";

$dataPopulated = true;

$productsData = $populator->readJson('data/SAMPLE_hm_product_list.json');
$mockData = $populator->readJson('data/mock_data.json');

$populator->importProducts($mysqli, $productsData);
$populator->importCustomers($mysqli, $mockData);
$populator->importMemberships($mysqli, $mockData);
$populator->importOrders($mysqli, $mockData);
$populator->importReviews($mysqli, $mockData);

$endTime = microtime(true);
$executionTime = $endTime - $startTime;
echo "Data written to '" . $dbname . "' tables in " . number_format($executionTime, 2) . " seconds.<br /><br />";

// Close the connection
$mysqli->close();
?>