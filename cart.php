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
<a href="cart.php" class="initbutton buttonBlue">Production Data</a>
<a href="cart.php?data=sample" class="initbutton buttonOrange">Sample Data</a>
<br/><br/>

<h2>Add Product to Cart</h2>

<form method="post" action="" class="fancyform">
    <label>Product ID: <input type="number" name="productID" required></label><br>
    <label>Quantity: <input type="number" name="quantity" required></label><br>
    <button type="submit" name="add_product">Add Item</button>
</form>

<style>
    /* General styling for the page */
    /* Header styling */
    h1, h2 {
        color: #333;
    }

    .place-order-button {
            background-color: #FF6600;
            color: white; 
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .place-order-button:hover {
            background-color: #CC5200; 
        }

        .remove-button {
            background-color: #FF4C4C; /* Base color: Light red */
            color: white; /* Text color */
            border: none;
            padding: 8px 16px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth transition */
            border-radius: 4px; /* Optional: rounded corners */
        }
        .remove-button:hover {
            background-color: #FF1A1A; /* Darker red on hover */
        }

    /* Table styling */
    table {
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
        color: #333;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Cart empty message styling */
    .empty-cart {
        color: #888;
        font-style: italic;
    }
</style>

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
echo "<form method='post' action=''><button type='submit' class='remove-button' name='remove_all'>Empty Cart</button></form><br/>";
$orderTotal = 0;

// Fetch the cart data
$sql = "SELECT productID, count FROM cart WHERE customerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {   
    echo "<table border='1'>";
    echo "<tr><th>Article ID</th><th>Product Name</th><th>Quantity</th><th>Unit Price</th><th>Total Price</th><th>Action</th></tr>";

    while ($row = $result->fetch_assoc()) {
        $cartProductID = $row['productID'];
        $cartArticleID = $row['productID'];
        $cartCount = intval($row['count']); 

        // Check productColors table for the articleID
        $stmt = $conn->prepare("SELECT productID FROM productColors WHERE articleID = ?");
        $stmt->bind_param("i", $cartProductID);
        $stmt->execute();
        $colorResult = $stmt->get_result();

        if ($colorResult->num_rows > 0) {
            $colorRow = $colorResult->fetch_assoc();
            $cartProductID = $colorRow['productID'];
        }

        // Fetch product name
        $stmt = $conn->prepare("SELECT productName FROM products WHERE productID = ?");
        $stmt->bind_param("i", $cartProductID);
        $stmt->execute();
        $productResult = $stmt->get_result();
        if ($productResult->num_rows > 0) {
            $productRow = $productResult->fetch_assoc();
            $cartProductName = $productRow['productName'];
        } else {
            // If no product name found, set a default
            $cartProductName = "Unknown Product";
        }

        // Fetch the correct price
        $sql = "SELECT price FROM productPrices WHERE productID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cartProductID);
        $stmt->execute();
        $priceResult = $stmt->get_result();

        $prices = [];

        // Fetch and store all prices into an array
        while ($priceRow = $priceResult->fetch_assoc()) {
            $prices[] = floatval($priceRow['price']); // Ensure each price is a float
        }

        // Determine the lowest price from the array
        if (!empty($prices)) {
            $price = min($prices); // Get the minimum price
        } else {
            $price = 0; // Default value if no prices are found
        }

        // Calculate total for this product
        $productTotal = $price * $cartCount;
        $orderTotal += $productTotal;

        // Display the product details in the table
        echo "<tr>";
        echo "<td><a href='product.php?id=" . $cartProductID . "&color=" . $cartArticleID . "'>" . $cartArticleID . "</a></td>";
        echo "<td>" . $cartProductName . "</td>";
        echo "<td>" . $cartCount . "</td>";
        echo "<td>$" . number_format($price, 2) . "</td>";
        echo "<td>$" . number_format($productTotal, 2) . "</td>";
        echo "<td><form method='post' action=''><button type='submit' class='remove-button' name='remove_product' value='" . $cartArticleID . "'>Remove</button></form></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<p><big>Total: $" . number_format($orderTotal, 2) . "</big></p>";
} else {
    echo "<p>Your cart is empty.</p><br/><br/>";
}

// Remove product from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_product'])) {
    $productID = intval($_POST['remove_product']);
    if ($conn->query("DELETE FROM cart WHERE customerID = $userID AND productID = $productID") === TRUE) {
        echo "<p>Product removed successfully.</p>";
    } else {
        echo "<p>Error removing product: " . $conn->error . "</p>";
    }
    header("Location: cart.php");
    exit();
}

//Empty cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_all'])) {
    $productID = intval($_POST['remove_product']);
    if ($conn->query("DELETE FROM cart WHERE customerID = $userID") === TRUE) {
        echo "<p>Cart emptied.</p>";
    } else {
        echo "<p>Error emptying cart: " . $conn->error . "</p>";
    }
    header("Location: cart.php");
    exit();
}
?>

<h2>Apply Promo Code</h2>

<form method="post" action="" class="fancyform">
    <label>Promo Code: <input type="text" name="promoCode" required></label><br>
    <button type="submit" name="apply_promo">Apply Promo Code</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_promo'])) {
    $promoCode = $conn->real_escape_string($_POST['promoCode']);

    // Check if the promo code exists and is available
    $stmt = $conn->prepare("SELECT * FROM promoCodes WHERE promoCode = ? AND totalAvailable > 0");
    $stmt->bind_param("s", $promoCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<p>Promo code not valid.</p>";
    } else {
        $promo = $result->fetch_assoc();
        
        // Check if the promo code is member-only
        if ($promo['isMemberOnly'] == 1) {
            $stmt = $conn->prepare("SELECT * FROM memberships WHERE customerID = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $membershipResult = $stmt->get_result();
            
            if ($membershipResult->num_rows === 0) {
                echo "<p>This promo code is for membership-holders only.</p>";
            } else {
                applyDiscount($promo, $orderTotal);
            }
        } else {
            applyDiscount($promo, $orderTotal);
        }
    }
}

function applyDiscount($promo, $orderTotal) {
    global $conn;
    if ($promo['discountType'] == 'amount_off' && $orderTotal > $promo['restrictionAmount']) {
        $discountAmount = $promo['discountAmount'];
        $newTotal = $orderTotal - $discountAmount;
        echo "<p>Promo code applied! Your new total is: <br/><big>$" . number_format($newTotal, 2) . "</big></p>";
    } elseif ($promo['discountType'] == 'percent_off' && $orderTotal < $promo['restrictionAmount']) {
        $discountAmount = ($promo['discountAmount'] / 100) * $orderTotal;
        $newTotal = $orderTotal - $discountAmount;
        echo "<p>Promo code applied! Your new total is: <br/><big>$" . number_format($newTotal, 2) . "</big></p>";
    } else {
        echo "<p>Promo code cannot be applied to this order.</p>";
    }
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

<form method="post" action="" style="text-align: right;">
    <button type="submit" name="place_order" class="place-order-button"><big><b>Place Order</b></big></button>
</form>

<?php $conn->close(); ?>

<?php include 'includes/footer.php'; ?>