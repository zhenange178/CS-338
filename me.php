<?php include 'includes/header.php'; ?>
<?php
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "sampledatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL: select the user's customer entry (100000)
$sql = "SELECT * FROM customers WHERE customerID = 100000";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
?>

<h1>My Account</h1>
<p>View information and orders.</p>
<h2>User Information</h2>

<form method="post">
    <input type="hidden" name="customerID" value="<?php echo $customer['customerID']; ?>">
    ID: 100000<br/>
    
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

<?php
if (isset($_POST['save'])) {
    $id = $_POST['customerID'];
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
        echo "Changes updated successfully.";
    } else {
        echo "Error updating customer: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<h2>Order History</h2>


<?php include 'includes/footer.php'; ?>