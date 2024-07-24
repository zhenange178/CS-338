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

// Create the cart table if it doesn't exist
$conn->query("
    CREATE TABLE IF NOT EXISTS cart (
        customerID INT,
        productID INT,
        count INT,
        PRIMARY KEY (customerID, productID)
    )
");
?>
<h1>Place an Order</h1>

You are now using the <b><?php echo $dbType; ?></b> database. Choose an option below: <br/><br/>
<a href="place_order.php" class="initbutton buttonBlue">Production Data</a>
<a href="place_order.php?data=sample" class="initbutton buttonOrange">Sample Data</a>
<br/><br/>

<h2>Add Product to Cart</h2>

<form method="post" action="">
    <label>Product ID: <input type="number" name="productID" required></label><br>
    <label>Quantity: <input type="number" name="quantity" required></label><br>
    <button type="submit" name="add_product">Add Item</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $productID = intval($_POST['productID']);
    $quantity = intval($_POST['quantity']);

    $result = $conn->query("SELECT articleID FROM productColors WHERE articleID = $productID");
    if ($result->num_rows > 0) {
        // Check if the product is already in the cart
        $checkCart = $conn->query("SELECT count FROM cart WHERE customerID = $userID AND productID = $productID");
        if ($checkCart->num_rows > 0) {
            // Update the existing product quantity
            $conn->query("UPDATE cart SET count = count + $quantity WHERE customerID = $userID AND productID = $productID");
        } else {
            // Insert the new product into the cart
            $conn->query("INSERT INTO cart (customerID, productID, count) VALUES ($userID, $productID, $quantity)");
        }
        echo "<p>Product added to cart successfully!</p>";
    } else {
        echo "<p>Invalid product ID</p>";
    }
}

// Display the cart
echo "<h2>Your Cart</h2>";
$sql = "SELECT p.productID, p.productName, c.count
        FROM cart c
        JOIN productColors pc ON c.productID = pc.articleID
        JOIN products p ON pc.productID = p.productID
        WHERE c.customerID = ?";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Product/Article ID</th><th>Product Name</th><th>Quantity</th><th>Action</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['productID'] . "</td>";
        echo "<td>" . $row['productName'] . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "<td><form method='post' action=''><button type='submit' name='remove_product' value='" . $row['productID'] . "'>Remove</button></form></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Your cart is empty.</p>";
}

// Remove product from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_product'])) {
    $productID = intval($_POST['remove_product']);
    $conn->query("DELETE FROM cart WHERE customerID = $userID AND productID = $productID");
    header("Location: place_order.php");
    exit();
}

// Place the order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Generate a random tracking ID
    $trackingID = mt_rand(100000, 999999);

    // Insert the order
    $stmt = $conn->prepare("INSERT INTO orders (customerID, trackingID, orderDateTime) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $userID, $trackingID);
    $stmt->execute();
    $orderID = $stmt->insert_id;
    $stmt->close();

    // Insert each product from the cart into orderDetails
    $result = $conn->query("SELECT productID, count FROM cart WHERE customerID = $userID");
    $stmt = $conn->prepare("INSERT INTO orderDetails (orderID, productID, count) VALUES (?, ?, ?)");
    while ($row = $result->fetch_assoc()) {
        $stmt->bind_param("iii", $orderID, $row['productID'], $row['count']);
        $stmt->execute();
    }
    $stmt->close();

    echo "<p>Order placed successfully! Your tracking ID is $trackingID.</p>";

    // Clear the cart
    $conn->query("DELETE FROM cart WHERE customerID = $userID");
    
    // Redirect to order details page with the newly inserted orderID
    header("Location: order_details.php?id=$orderID");
    exit();
}
?>

<h2>Place Order</h2>

<form method="post" action="">
    <button type="submit" name="place_order">Place Order</button>
</form>

<?php $conn->close(); ?>

<?php include 'includes/footer.php'; ?>