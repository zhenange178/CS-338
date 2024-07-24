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

// Retrieve order details
$sqlOrder = "SELECT * FROM orders WHERE orderID = ?";
$stmt = $conn->prepare($sqlOrder);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

if ($order) {
    echo "<h1>Order Confirmation</h1>";
    echo "<p>Order ID: " . htmlspecialchars($order['orderID']) . "</p>";
    echo "<p>Tracking ID: " . htmlspecialchars($order['trackingID']) . "</p>";
    echo "<p>Order Date: " . htmlspecialchars($order['orderDateTime']) . "</p>";
    echo "<p>Customer ID: " . $customerID . "</p>";

    $sqlOrderDetails = "
    SELECT productID, count
    FROM orderDetails
    WHERE orderID = ?
";
$stmt = $conn->prepare($sqlOrderDetails);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$orderDetailsResult = $stmt->get_result();

echo "<h2>Items Ordered</h2>";
echo "<table border='1'>";
echo "<tr><th>Product ID</th><th>Quantity</th></tr>";

// Fetch and display order details
while ($row = $orderDetailsResult->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['productID']) . "</td>";
    echo "<td>" . htmlspecialchars($row['count']) . "</td>";
    echo "</tr>";
}

echo "</table>";
} else {
    echo "<p>Order not found.</p>";
}

$stmt->close();
$conn->close();
// Include footer
include 'includes/footer.php';
?>