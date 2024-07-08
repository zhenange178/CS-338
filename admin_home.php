<?php include 'includes/header.php'; ?>
<?php
// include
require 'includes/ViewDB.php';
// Database credentials
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<h1>Admin Center — Production</h1>
<h2>Sample Database Admin</h2>
For sample database admin center, click <a href="admin_home_sample.php">here</a>.<br/>

<div>
<h2>Top 10 Most Used Promo Codes</h2>
<?php
// SQL: Select no null promo codes, sort by most used
$sql = "SELECT promoCodeUsed, COUNT(*) as orderCount FROM orders WHERE promoCodeUsed IS NOT NULL GROUP BY promoCodeUsed ORDER BY orderCount DESC LIMIT 10";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table style='border-collapse: collapse; width: 100%; border: 1px solid #ddd;'>";
    echo "<tr><th style='border: 1px solid #ddd; padding: 8px;'>Promo Code</th><th style='border: 1px solid #ddd; padding: 8px;'>Order Count</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>".$row["promoCodeUsed"]."</td><td style='border: 1px solid #ddd; padding: 8px;'>".$row["orderCount"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "No results found";
}

$conn->close();
?>
<h3>Retrieve Promo Code Attributes</h3>
    <form method="post">
        <label for="promoCode">Enter Promo Code:</label>
        <input type="text" id="promoCode" name="promoCode" required>
        <br>
        <input type="submit" name="submit_search">
    </form>
</div>
<?php
if (isset($_POST['submit_search'])) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve promo code from form submission
    $promoCode = $_POST["promoCode"];

    // Prepare SQL statement to retrieve promo code attributes
    $sql = "SELECT discountType, discountAmount, restrictionAmount
            FROM PromoCodes
            WHERE promoCode = ?";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $promoCode);

    // Execute the query
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($discountType, $discountAmount, $restrictionAmount);

    // Fetch values
    if ($stmt->fetch()) {
        // Display retrieved attributes
        echo "<h3>Promo Code Attributes</h3>";
        echo "<table>";
        echo "<tr><th>Promo Code</th><th>Discount Type</th><th>Discount Amount</th><th>Restriction Amount</th></tr>";
        echo "<tr><td>$promoCode</td><td>$discountType</td><td>$discountAmount</td><td>$restrictionAmount</td></tr>";
        echo "</table>";
    } else {
        echo "<p>Promo code '$promoCode' not found.</p>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<h2></h2>
<?php include 'includes/footer.php'; ?>