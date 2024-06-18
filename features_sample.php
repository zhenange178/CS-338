<?php include 'includes/header.php'; ?>
<?php
// include
require 'includes/ViewDB.php';
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

<div>
    <h3>View Tables</h3>
    View all tables <a href="viewDB_sample.php">here</a>.<br />
</div>

<div>
    <h3>Search Products</h3>
    <form method="post">
        <label>Product Name: <input type="text" name="name"></label><br>
        <label>Product ID: <input type="number" name="id"></label><br>
        <label>Attribute:
            <select name="attribute">
                <option value="all_attribute">All</option>
                <option value="New Arrival">New Arrival</option>
            </select>
        </label><br>
        <label>Availability:
            <select name="availability">
                <option value="all_availability">All</option>
                <option value="Available">Available</option>
                <option value="NotAvailable">Out of stock</option>
            </select>
        </label><br>
        <button type="submit" name="submit_search">Search</button>
    </form>

    <?php
    //search form
    if (isset($_POST['submit_search'])) {
        // Retrieve form data
        $name = $_POST['name'] ?? '';
        $id = $_POST['id'] ?? '';
        $attribute = $_POST['attribute'] ?? 'all_attribute';
        $availability = $_POST['availability'] ?? 'all_availability';

        // Construct SQL query
        $query = "SELECT * FROM products WHERE 1=1";
        
        // Add conditions based on input
        if (!empty($name)) {
            $query .= " AND productName LIKE '%" . mysqli_real_escape_string($conn, $name) . "%'";
        }
        if (!empty($id)) {
            $query .= " AND productID LIKE '%" . mysqli_real_escape_string($conn, $id) . "%'";
        }
        if ($attribute != 'all_attribute') {
            $query .= " AND sellingAttribute = '" . mysqli_real_escape_string($conn, $attribute) . "'";
        }
        if ($availability != 'all_availability') {
            $query .= " AND stock = '" . mysqli_real_escape_string($conn, $availability) . "'";
        }
        echo "<code>$query</code>";
        echo"<br/>";
        $tableDisplay = new ViewDB($conn);
        $tableDisplay->displayTable($query);

    }
    $conn->close();
    ?>
</div>

<?php include 'includes/footer.php'; ?>