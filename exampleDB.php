<?php
$servername = "127.0.0.1";
$username = "user1";
$password = "password"; 

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
echo "Initializing Database... <br>";
$sql = "CREATE DATABASE IF NOT EXISTS testDB";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("testDB");

// SQL to create table
$sql = "CREATE TABLE IF NOT EXISTS student (
    uid DECIMAL(3, 0) NOT NULL PRIMARY KEY,
    name VARCHAR(30),
    score DECIMAL(3, 2)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table student created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Insert data into table
$sql = "INSERT IGNORE INTO student (uid, name, score) VALUES
(1, 'Alice', 0.1),
(2, 'Bob', 0.4),
(3, 'Charlie', 3.7),
(4, 'David', 6.8),
(5, 'Eva', 2.3),
(6, 'Fiona', 7.8),
(7, 'George', 5.4),
(8, 'Hannah', 1.2),
(9, 'Ian', 8.6),
(10, 'Julia', 9.0),
(11, 'Kyle', 3.2),
(12, 'Laura', 4.5),
(13, 'Max', 8.1),
(14, 'Nina', 5.9),
(15, 'Oliver', 2.1),
(16, 'Paula', 4.3),
(17, 'Quinn', 7.7),
(18, 'Rachel', 6.2),
(19, 'Steve', 3.3),
(20, 'Tina', 7.5);";

if ($conn->query($sql) === TRUE) {
    echo "Records inserted successfully, duplicates were ignored<br>";
} else {
    echo "Error: " . $conn->error . "<br>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Student Entries</title>
</head>
<body>
    <form method="post">
        <button type="submit" name="view_entries">View All Entries</button>
        <button type="submit" name="clear">Clear</button>
    </form>
    <form method="post">
        <label>Name: <input type="text" name="name"></label><br>
        <label>Score Threshold: <input type="number" name="score" step="0.1"></label><br>
        <label>Threshold Type:
            <select name="threshold_type">
                <option value="min">Minimum</option>
                <option value="max">Maximum</option>
            </select>
        </label><br>
        <button type="submit" name="submit_search">Search</button>
    </form>

<?php
//view all button
if (isset($_POST['view_entries'])) {
    // Select and display data
    $sql = "SELECT uid, name, score FROM student";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "uid: " . $row["uid"] . " - Name: " . $row["name"] . " - Score: " . $row["score"] . "<br>";
        }
    } else {
        echo "0 results<br>";
    }
}

//clear all button
if (isset($_POST['clear'])) {
    echo "";
}

//search form
if (isset($_POST['submit_search'])) {
    $name = $_POST['name'];
    $score = $_POST['score'];
    $thresholdType = $_POST['threshold_type'];

    $sql = "SELECT uid, name, score FROM student WHERE ";
    $conditions = [];

    if (!empty($name)) {
        $conditions[] = "name LIKE '%" . $conn->real_escape_string($name) . "%'";
    }
    if (!empty($score)) {
        if ($thresholdType == "min") {
            $conditions[] = "score >= " . floatval($score);
        } else {
            $conditions[] = "score <= " . floatval($score);
        }
    }

    if (count($conditions) > 0) {
        $sql .= implode(" AND ", $conditions);
    } else {
        $sql .= "1"; // Always true condition to fetch all if no filters are set
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<p>Results:</p>";
        while($row = $result->fetch_assoc()) {
            echo "UID: " . $row["uid"] . " - Name: " . $row["name"] . " - Score: " . $row["score"] . "<br>";
        }
    } else {
        echo "No results found.<br>";
    }
}
$conn->close();
?>

</body>
</html>