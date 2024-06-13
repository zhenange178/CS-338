<?php

// Database connection parameters
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "sampledatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "DROP TABLE IF EXISTS reviews";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Get column names
    $fields = $result->fetch_fields();

    // Display column names
    echo "<table border='1'>";
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";

    // Display rows
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . $value . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}

$conn->close();

?>