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
$dbname = "hmdatabase";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$populator = new DatabasePopulator();

echo "<b>Populating Database...</b><br />";
$startTime = microtime(true);
echo "Writing data to tables...<br />";
$productsData = $populator->readJson('data/hm_product_list.json');

$populator->importProducts($mysqli, $productsData);

$endTime = microtime(true);
$executionTime = $endTime - $startTime;
echo "Data written to '" . $dbname . "' tables in " . number_format($executionTime, 2) . " seconds.<br /><br />";

// Close the connection
$mysqli->close();
?>