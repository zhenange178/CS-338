<?php include 'includes/header.php'; ?>
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

?>

<h1>Features — Sample Database</h1>

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

<?php include 'includes/footer.php'; ?>