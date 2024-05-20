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
        <button type="submit" name="specify_score">Specify Score Threshold</button>
        <button type="submit" name="clear">Clear</button>
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

//score threshold
if (isset($_POST['specify_score'])) {
    // Display the threshold form
    echo 'Display entries that meet a certain score threshold.<br>
          <form method="post">
            <label for="threshold">Enter Score Threshold:</label>
            <input type="number" id="threshold" name="threshold" step="0.1" min="0" max="10">
            <button type="submit" name="submit_threshold">Display Scores</button>
          </form>';
}

if (isset($_POST['submit_threshold'])) {
    $threshold = $_POST['threshold'];
    // Select and display data above the specified threshold
    $sql = "SELECT uid, name, score FROM student WHERE score > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("d", $threshold);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "uid: " . $row["uid"] . " - Name: " . $row["name"] . " - Score: " . $row["score"] . "<br>";
        }
    } else {
        echo "No results for scores over " . $threshold . "<br>";
    }
}

//clear all button
if (isset($_POST['view_entries'])) {
    echo "";
}
$conn->close();
?>

</body>
</html>