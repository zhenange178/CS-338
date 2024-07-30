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
$urlEnd = "";

if (isset($_GET['data']) && $_GET['data'] === 'sample') {
    $dbname = "sampledatabase";
    $dbType = "sample";
    $urlEnd = "&data=sample";
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<h1> Order Details</h1>

<?php
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
    echo "<p>Order ID: " . htmlspecialchars($order['orderID']) . "</p>";
    echo "<p>Tracking ID: " . htmlspecialchars($order['trackingID']) . "</p>";
    echo "<p>Order Date: " . htmlspecialchars($order['orderDateTime']) . "</p>";
    echo "<p>Customer ID: " . htmlspecialchars($order['customerID']) . "</p>";

    // Retrieve order details
    $sqlOrderDetails = "
        SELECT od.productID, od.count
        FROM orderDetails od
        WHERE od.orderID = ?
    ";
    $stmt = $conn->prepare($sqlOrderDetails);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $orderDetailsResult = $stmt->get_result();

    echo "<h2>Items Ordered</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Product Link</th><th>Quantity</th></tr>";

    // Fetch and display order details with clickable product links
    while ($row = $orderDetailsResult->fetch_assoc()) {
        $productID = htmlspecialchars($row['productID']);
        $quantity = htmlspecialchars($row['count']);

        // Check if productID is in the products table
        $sqlCheckProduct = "SELECT productID FROM products WHERE productID = ?";
        $stmtCheckProduct = $conn->prepare($sqlCheckProduct);
        $stmtCheckProduct->bind_param("i", $productID);
        $stmtCheckProduct->execute();
        $resultProduct = $stmtCheckProduct->get_result();

        if ($resultProduct->num_rows > 0) {
            // Product ID exists in products table
            $link = "product.php?id=$productID";
        } else {
            // Product ID does not exist in products table; check in productColors table
            $sqlCheckColor = "SELECT productID FROM productColors WHERE articleID = ?";
            $stmtCheckColor = $conn->prepare($sqlCheckColor);
            $stmtCheckColor->bind_param("i", $productID);
            $stmtCheckColor->execute();
            $resultColor = $stmtCheckColor->get_result();

            if ($resultColor->num_rows > 0) {
                $colorRow = $resultColor->fetch_assoc();
                $linkedProductID = $colorRow['productID'];
                $link = "product.php?id=$linkedProductID&color=$productID";
            } else {
                $link = "#"; // Default link if neither condition is met
            }
        }

        echo "<tr>";
        echo "<td><a href='$link'>$productID</a></td>";
        echo "<td>$quantity</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Order not found.</p>";
}
?>

<?php 
$sqlCheckReturn = "
SELECT returnDateTime
FROM returnedOrders
WHERE orderID = ?
";
$stmt = $conn->prepare($sqlCheckReturn);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $return = $result->fetch_assoc();
    $returnDateTime = $return['returnDateTime'];
    echo "<p>This order has been returned on " . htmlspecialchars($returnDateTime) . ".</p>";
} else {
?>
<br/><br/>
<a href="return_order.php?id=<?php echo $orderID; ?><?php echo $urlEnd; ?>" class="initbutton buttonRed"><b>Return Order</b></a>
<br/><br/>
<?php } ?>

<?php
$stmt->close();
$conn->close();
// Include footer
include 'includes/footer.php';
?>