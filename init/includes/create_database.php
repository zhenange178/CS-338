<?php
// includes
include 'DatabaseCreator.php';

ob_implicit_flush(1);
while (ob_get_level()) {
    ob_end_flush();
}

set_time_limit(120);

$servername = "127.0.0.1";
$username = "user1";
$password = "password"; 
$dbName = 'hmdatabase';

// Create connection
$mysqli = new mysqli($servername, $username, $password);

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$creator = new DatabaseCreator();
$creator->createDatabase($mysqli, $dbName);

$mysqli->close();

?>