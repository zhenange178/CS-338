<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['userID']) || $_SESSION['userID'] > 100100) {
    header("Location: /");
    exit();
}
$userID = $_SESSION['userID'];

// Include header
include 'includes/header.php';

// Database connection details
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";
$dbType = "production";

if (isset($_GET['data']) && $_GET['data'] === 'sample') {
    $dbname = "sampledatabase";
    $dbType = "sample";
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$orderID = intval($_GET['id']);

echo "<h1> Return Order</h1>";

// Fetch customerID for the given orderID
$sqlCheckUser = "
    SELECT customerID
    FROM orders
    WHERE orderID = ?
";
$stmt = $conn->prepare($sqlCheckUser);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No such order
    echo "Order not found.";
    $conn->close();
    exit();
}

$order = $result->fetch_assoc();
$customerID = $order['customerID'];

// Make sure logged in customer is customer who placed order
if ($userID != $customerID){
    header("Location: /");
    exit();
}

// Retrieve order details
$sqlOrder = "SELECT * FROM orders WHERE orderID = ?";
$stmt = $conn->prepare($sqlOrder);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

if ($order) {
    $orderID = $order['orderID'];
    echo "<p>Order ID: " . $orderID . "</p>";
} else {
    echo "<p>Order not found.</p>";
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $returnReason = $_POST['returnReason'];
    $returnDateTime = date('Y-m-d H:i:s');

    $sqlReturn = "
        INSERT INTO returnedOrders (orderID, returnDateTime, returnReason)
        VALUES (?, ?, ?)
    ";
    $stmt = $conn->prepare($sqlReturn);
    $stmt->bind_param("iss", $orderID, $returnDateTime, $returnReason);

    if ($stmt->execute()) {
        echo "<p>Return processed successfully.</p>";
        if ($dbType == 'production'){
            header("Location: order_details.php?id=$orderID");
        } else {
            header("Location: order_details.php?id=$orderID&data=sample");
        }
        exit();
    } else {
        echo "<p>Error processing return: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

?>

<form method="POST">
    <label for="returnReason">Reason for return:</label>
    <select id="returnReason" name="returnReason" required>
        <option value="Defective item">Defective item</option>
        <option value="Wrong item shipped">Wrong item shipped</option>
        <option value="Item not as described">Item not as described</option>
        <option value="Changed mind">Changed mind</option>
        <option value="Found a better price">Found a better price</option>
    </select>
    <br/><br/>
    <input type="submit" value="Submit Return" class="initbutton buttonRed">
</form>

<?php
$conn->close();
// Include footer
include 'includes/footer.php';
?>
