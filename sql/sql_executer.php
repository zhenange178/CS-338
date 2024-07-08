<?php
// Database credentials
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "sampledatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT customerID, COUNT(orderID) as order_count 
INTO OUTFILE 'C:\\ProgramData\\MySQL\\MySQL Server 8.0\\Uploads\\F06_production.csv'
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
LINES TERMINATED BY '\n'
FROM orders 
Group By customerID 
Order By order_count 
DESC
LIMIT 1;
";

$result = $conn->query($sql);

$conn->close();
?>