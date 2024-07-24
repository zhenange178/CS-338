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
?>
<h1>Place an Order</h1>

You are now using the <b><?php echo $dbType; ?></b> database. Choose an option below: <br/><br/>
<a href="place_order.php" class="initbutton buttonBlue">Production Data</a>
<a href="place_order.php?data=sample" class="initbutton buttonOrange">Sample Data</a>
<br/><br/>

<h2>Add Products to Order</h2>

<form method="post" action="">
    <?php for ($i = 0; $i < 5; $i++): ?>
        <label>Product ID: <input type="number" name="products[<?php echo $i; ?>][productID]" required></label><br>
        <label>Quantity: <input type="number" name="products[<?php echo $i; ?>][quantity]" required></label><br><br>
    <?php endfor; ?>
    <button type="submit" name="place_order">Place Order</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $products = $_POST['products'];
    $validOrder = true;
    $orderItems = [];
    $grandTotal = 0;

    foreach ($products as $product) {
        $productID = intval($product['productID']);
        $quantity = intval($product['quantity']);
        if ($productID > 0 && $quantity > 0) {
            $result = $conn->query("SELECT price, priceType FROM productPrices WHERE productID = $productID");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $price = ($row['priceType'] == 'redPrice') ? $row['price'] : $row['price'];
                $totalPrice = $price * $quantity;
                $grandTotal += $totalPrice;
                $orderItems[] = ['productID' => $productID, 'quantity' => $quantity, 'totalPrice' => $totalPrice];
            } else {
                echo "<p>Invalid product ID: $productID</p>";
                $validOrder = false;
            }
        }
    }

    if ($validOrder && !empty($orderItems)) {
        // Generate a random tracking ID
        $trackingID = mt_rand(100000, 999999);

        // Insert the order
        $stmt = $conn->prepare("INSERT INTO orders (customerID, trackingID, orderDateTime) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $userID, $trackingID);
        $stmt->execute();
        $orderID = $stmt->insert_id;
        $stmt->close();

        // Insert each product into orderDetails
        $stmt = $conn->prepare("INSERT INTO orderDetails (orderID, productID, count) VALUES (?, ?, ?)");
        foreach ($orderItems as $item) {
            $stmt->bind_param("iii", $orderID, $item['productID'], $item['quantity']);
            $stmt->execute();
        }
        $stmt->close();

        echo "<p>Order placed successfully! Your tracking ID is $trackingID.</p>";
        echo "<h3>Grand Total: \$$grandTotal</h3>";
    }
}

$conn->close();
?>

<?php include 'includes/footer.php'; ?>
