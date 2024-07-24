<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['userID'])) {
    header("Location: /");
    exit();
}
$userID = $_SESSION['userID'];
?>

<?php include 'includes/header.php'; ?>
<?php
$userID = $_SESSION['userID'];
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";
$dbType = "production";
if (isset($_GET['data'])) {
    if ($_GET['data'] === 'sample'){
        $dbname = "sampledatabase";
        $dbType = "sample";
    }
}

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL: select the user's customer entry
$sql = "SELECT * FROM customers WHERE customerID = " . $userID;
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
?>

<h1>My Account</h1>

You are now using the <b><?php echo $dbType; ?></b> database. Choose an option below: <br/><br/>
<a href="me.php" class="initbutton buttonBlue">Production Data</a>
<a href="me.php?data=sample" class="initbutton buttonOrange">Sample Data</a>
<br/><br/>

<h2>User Information</h2>

<form method="post">
    <?php
        echo "ID: " . $userID;
    ?>
    <br/>
    
    <label for="fname">First Name:</label><br/>
    <input type="text" id="fname" name="fname" size="50" value="<?php echo $customer['firstName']; ?>"><br>

    <label for="lname">Last Name:</label><br/>
    <input type="text" id="lname" name="lname" size="50" value="<?php echo $customer['lastName']; ?>"><br>

    <label for="email">Email:</label><br/>
    <input type="text" id="email" name="email" size="50" value="<?php echo $customer['customerEmail']; ?>"><br>
    
    <label for="phone">Phone:</label><br/>
    <input type="text" id="phone" name="phone" size="50" value="<?php echo $customer['customerPhone']; ?>"><br>
    
    <label for="address">Address:</label><br/>
    <input type="text" id="address" name="address" size="50" value="<?php echo $customer['customerAddress']; ?>"><br>

    <label for="birthday">Birthday:</label><br/>
    <input type="date" id="birthday" name="birthday" value="<?php echo $customer['customerBirth']; ?>"><br><br>

    <button type="submit" name="save">Save Changes</button>
</form>
<style>
    table {
        
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    th {
        background-color: #f4f4f4;
    }
    a {
        color: #007bff;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>

<?php
// Fetch orders for the logged-in user
$sqlOrders = "SELECT * FROM orders WHERE customerID = ?";
$stmt = $conn->prepare($sqlOrders);
$stmt->bind_param("i", $userID);
$stmt->execute();
$ordersResult = $stmt->get_result();

// Fetch reviews for the logged-in user
$sqlReviews = "SELECT * FROM reviews WHERE customerID = ?";
$stmt = $conn->prepare($sqlReviews);
$stmt->bind_param("i", $userID);
$stmt->execute();
$reviewsResult = $stmt->get_result();
?>
<h2>My Orders</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Tracking ID</th>
        <th>Order Date & Time</th>
        <th>Promo Code Used</th>
    </tr>

    <?php while ($row = $ordersResult->fetch_assoc()): ?>
    <tr>
        <td><a href="order_details.php?id=<?php echo htmlspecialchars($row['orderID']); ?>"><?php echo htmlspecialchars($row['orderID']); ?></a></td>
        <td><?php echo htmlspecialchars($row['trackingID']); ?></td>
        <td><?php echo htmlspecialchars($row['orderDateTime']); ?></td>
        <td><?php echo $row['promoCodeUsed']; ?></td>
    </tr>
    <?php endwhile; ?>

</table>

<h2>My Reviews</h2>
<table>
    <tr>
        <th>Review ID</th>
        <th>Product ID</th>
        <th>Rating</th>
        <th>Comment</th>
    </tr>

    <?php while ($row = $reviewsResult->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['reviewID']); ?></td>
        <td><a href="product.php?id=<?php echo htmlspecialchars($row['productID']); ?>"><?php echo htmlspecialchars($row['productID']); ?></a></td>
        <td><?php echo htmlspecialchars($row['rating']); ?></td>
        <td><?php echo htmlspecialchars($row['comment']); ?></td>
    </tr>
    <?php endwhile; ?>

</table>

<?php
if (isset($_POST['save'])) {
    $id = $userID;
    $firstName = $_POST['fname'];
    $lastName = $_POST['lname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birth = strtotime($_POST['birthday']);
    $birthday = date('Y-m-d H:i:s', $birth); 

    // SQL: update customer data according to form input
    $sql = "UPDATE customers SET firstName = ?, lastName = ?, customerBirth = ?, customerEmail = ?, customerPhone = ?, customerAddress = ?  WHERE customerID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $firstName, $lastName, $birthday, $email, $phone, $address, $id);

    if ($stmt->execute()) {
        header("Location: me.php");
        exit();
    } else {
        echo "Error updating customer: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
<br/><br/><br/>
<a href="logout.php" class="initbutton buttonRed"><b>Logout</b></a>
<br/><br/>

<?php include 'includes/footer.php'; ?>