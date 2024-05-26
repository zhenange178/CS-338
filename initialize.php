<?php
$servername = "127.0.0.1";
$username = "user1";
$password = "password"; 

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
echo "Initializing Database... <br>";
$sql = "CREATE DATABASE IF NOT EXISTS hmProducts";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("hmProducts");

// Drop existing table if it exists
$dropQuery = "DROP TABLE IF EXISTS products";
$conn->query($dropQuery);

// Create new table
$createQuery = "CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(255),
    name VARCHAR(255),
    stock_level INT,
    price DECIMAL(10, 2),
    currency VARCHAR(10),
    image_url VARCHAR(255),
    category_name VARCHAR(255),
    color VARCHAR(100),
    rgb_color VARCHAR(10)
)";
if ($conn->query($createQuery) === TRUE) {
    echo "Table created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Read and decode JSON
$jsonData = file_get_contents('hm_products_data.json');
$products = json_decode($jsonData, true);

// Iterate through products and insert into database
foreach ($products['results'] as $product) {
    // Check each key before using it to avoid errors
    $code = isset($product['code']) ? $conn->real_escape_string($product['code']) : '';
    $name = isset($product['name']) ? $conn->real_escape_string($product['name']) : '';
    $stockLevel = isset($product['stock']['stockLevel']) ? $product['stock']['stockLevel'] : 0;
    $price = isset($product['price']['value']) ? $product['price']['value'] : 0.0;
    $currency = isset($product['price']['currencyIso']) ? $product['price']['currencyIso'] : 'USD';
    $imageUrl = isset($product['images'][0]['url']) ? $conn->real_escape_string($product['images'][0]['url']) : '';
    $categoryName = isset($product['categoryName']) ? $conn->real_escape_string($product['categoryName']) : '';
    $color = isset($product['color']['text']) ? $conn->real_escape_string($product['color']['text']) : 'Unknown';
    $rgbColor = isset($product['rgbColor']) ? $conn->real_escape_string($product['rgbColor']) : '000000';

    // SQL query to insert data
    $sql = "INSERT INTO products (code, name, stock_level, price, currency, image_url, category_name, color, rgb_color)
            VALUES ('$code', '$name', $stockLevel, $price, '$currency', '$imageUrl', '$categoryName', '$color', '$rgbColor')";
    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error . "<br>";
    }
}

echo "Data inserted successfully!";
$conn->close();
?>