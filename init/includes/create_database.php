<?php
$servername = "127.0.0.1";
$username = "user1";
$password = "password"; 

echo "<h3>Creating Database</h3>";

// Create connection
$mysqli = new mysqli($servername, $username, $password);

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Check if exists, drop if needed, create. 
$dbname = "hmdatabase"; 

$result = $mysqli->query("SHOW DATABASES LIKE '$dbname'");
$exists = $result->num_rows > 0;

if ($exists) {
    if ($mysqli->query("DROP DATABASE $dbname") === TRUE) {
        echo "Old database dropped<br/>";
    } else {
        echo "Error dropping database: " . $mysqli->error . "<br/>";
    }
}

if ($mysqli->query("CREATE DATABASE IF NOT EXISTS hmdatabase")) {
    echo "Database created<br />";
} else {
    echo "Error creating database: " . $mysqli->error;
}

$mysqli->select_db($dbname);

/**
 * Create Tables
 */

// Create Product Table
$sql = "CREATE TABLE IF NOT EXISTS products (
    productID INT PRIMARY KEY,
    productName VARCHAR(255) NOT NULL,
    productURL VARCHAR(255) NOT NULL,
    productWeight FLOAT,
    sellingAttribute VARCHAR(255),
    stock VARCHAR(255) NOT NULL,
    comingSoon BOOLEAN NOT NULL
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table 'products' created<br />";
} else {
    echo "Error creating table 'products': " . $mysqli->error . "<br />";
}

// Create Customer Table
$sql = "CREATE TABLE IF NOT EXISTS customers (
    customerID INT PRIMARY KEY,
    customerBirth DATE NOT NULL,
    customerPhone VARCHAR(255) NOT NULL,
    customerAddress VARCHAR(255) NOT NULL,
    customerEmail VARCHAR(255) NOT NULL,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table 'customers' created<br />";
} else {
    echo "Error creating table 'customers': " . $mysqli->error . "<br />";
}

// Create membership Table
$sql = "CREATE TABLE IF NOT EXISTS memberships (
    membershipID INT PRIMARY KEY,
    customerID INT,
    membershipPrice FLOAT NOT NULL,
    memberRank INT NOT NULL,
    expirationDate DATE NOT NULL,
    CONSTRAINT fk_customer_membership
        FOREIGN KEY (customerID) 
        REFERENCES customers(customerID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table 'memberships' created<br />";
} else {
    echo "Error creating table 'memberships': " . $mysqli->error . "<br />";
}

// Create orders Table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    orderID INT PRIMARY KEY,
    customerID INT,
    trackingID INT NOT NULL,
    orderDateTime DATETIME NOT NULL,
    CONSTRAINT fk_customer_order
        FOREIGN KEY (customerID) 
        REFERENCES customers(customerID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table 'orders' created<br />";
} else {
    echo "Error creating table 'orders': " . $mysqli->error . "<br />";
}

// Create reviews Table
$sql = "CREATE TABLE IF NOT EXISTS reviews (
    reviewID INT PRIMARY KEY,
    customerID INT,
    productID INT,
    rating INT NOT NULL,
    comment VARCHAR(255) NOT NULL,
    CONSTRAINT fk_customer_review
        FOREIGN KEY (customerID) 
        REFERENCES customers(customerID)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_product_review
        FOREIGN KEY (productID) 
        REFERENCES products(productID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table 'reviews' created<br />";
} else {
    echo "Error creating table 'reviews': " . $mysqli->error . "<br />";
}

// Create promo codes Table
$sql = "CREATE TABLE IF NOT EXISTS promoCodes (
    promoCode VARCHAR(255) PRIMARY KEY,
    source VARCHAR(255),
    totalAvailable INT,
    isMemberOnly BOOLEAN NOT NULL,
    expiration DATE,
    discountType VARCHAR(255) NOT NULL,
    discountAmount FLOAT NOT NULL,
    restrictionAmount FLOAT
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table 'promoCodes' created<br />";
} else {
    echo "Error creating table 'promoCodes': " . $mysqli->error . "<br />";
}

?>