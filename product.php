<?php include 'includes/header.php'; ?>
<?php
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "sampledatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $product = getProductById($conn, $productId);

    if ($product) {
        echo "<h1>{$product['productName']}</h1>";
        echo "<p>ID: {$product['productID']}</p>";
    } else {
        echo "Product not found";
    }
} else {
    echo "Product ID is required";
}


function getProductById($conn, $productId) {
    $productId = mysqli_real_escape_string($conn, $productId);
    
    // SQL: get the particular product
    $sql = "SELECT * FROM products WHERE productID = '$productId'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        return $product;
    } else {
        return null;
    }
}
?>
<?php include 'includes/footer.php'; ?>