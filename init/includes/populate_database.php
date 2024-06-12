<?php
// Database credentials
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

/**
 * readJson
 * read a json file, return an encoded list
 * 
 * @param string $filename path to json file
 * @return JSON object
 */
function readJson($filename) {
    $jsonContent = file_get_contents($filename);
    return json_decode($jsonContent, true);
}

// Import data functions
function importProducts($mysqli, $productsData) {
    $stmt = $mysqli->prepare("INSERT IGNORE INTO products (productID, productName, productURL, productWeight, sellingAttribute, stock, comingSoon) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }
    
    foreach ($productsData['products'] as $product) {
        $stmt->bind_param("issdssi",
            $product['id'],
            $product['productName'],
            $product['url'],
            $product['quantity'],
            $product['sellingAttribute'],
            $product['availability']['stockState'],
            $product['availability']['comingSoon']
        );
        $stmt->execute();
    }
    echo "Products imported successfully.<br/>";
    $stmt->close();
}

function importProductCategories($mysqli, $productsData){
    $stmt = $mysqli->prepare("INSERT IGNORE INTO productCategories (productID, category, categoryType) VALUES (?, ?, ?)");

    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    foreach ($productsData['products'] as $product) {
        
        if (!isset($product['mainCatCode'])) {
            continue;
        }

        $categoryString = $product['mainCatCode'];
        $categories = explode('_', $categoryString);

        for ($i = 0; $i < count($categories); $i++){
            $stmt->bind_param("isi",
                $product['id'],
                $categories[$i],
                $i
            );
            $stmt->execute();
        }
    }
    echo "Product Categories imported successfully.<br/>";
    $stmt->close();
}

function importColors($mysqli, $productsData){
    // TODO: populate colors Table
    $stmt = $mysqli->prepare("INSERT IGNORE INTO productColors (articleID, productID, colorName, colorCode, articleImage) VALUES (?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    foreach ($productsData['products'] as $product) {
        
        if (!isset($product['swatches'])) {
            continue;
        }

        foreach ($product['swatches'] as $color){
            $stmt->bind_param("iisss",
                $color['articleId'],
                $product['id'],
                $color['colorName'],
                $color['colorCode'],
                $color['productImage']
            );
            $stmt->execute();
        }
    }
    echo "Product Colors imported successfully.<br/>";
    $stmt->close();
}

function importPrices($mysqli, $productsData){
    // TODO: populate prices Table
    $stmt = $mysqli->prepare("INSERT IGNORE INTO productPrices (productID, priceType, price) VALUES (?, ?, ?)");

    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    foreach ($productsData['products'] as $product) {
        
        if (!isset($product['prices'])) {
            continue;
        }

        foreach ($product['prices'] as $price){
            $stmt->bind_param("isd",
                $product['id'],
                $price['priceType'],
                $price['price']
            );
            $stmt->execute();
        }
    }
    echo "Product Prices imported successfully.<br/>";
    $stmt->close();
}

function importCustomers($mysqli, $customersData) {
    $stmt = $mysqli->prepare("INSERT IGNORE INTO customers (customerID, customerBirth, customerPhone, customerAddress, customerEmail, firstName, lastName) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    foreach ($customersData['customers'] as $customer) {
        $stmt->bind_param("issssss",
            $customer['ID'],
            $customer['Birth'],
            $customer['Phone'],
            $customer['Address'],
            $customer['Email'],
            $customer['FName'],
            $customer['LName']
        );

        if (!$stmt->execute()) {
            echo "Error importing customer with ID {$customer['ID']}: " . $stmt->error . "<br/>";
        } else {
            // echo "Customer with ID {$customer['ID']} imported successfully.<br/>";
        }
    }

    echo "Customers imported successfully.<br/>";
    $stmt->close();
}

function importMemberships($mysqli, $membershipsData) {
    $stmt = $mysqli->prepare("INSERT IGNORE INTO memberships (membershipID, customerID, membershipPrice, memberRank, expirationDate) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    foreach ($membershipsData['memberships'] as $membership) {
        $stmt->bind_param("iidsi",
            $membership['MemberID'],
            $membership['CustomerID'],
            $membership['Price'],
            $membership['Rank'],
            $membership['Expiration']
        );

        if (!$stmt->execute()) {
            echo "Error importing membership with ID {$membership['MemberID']}: " . $stmt->error . "<br/>";
        } else {
            // echo "Membership with ID {$membership['MemberID']} imported successfully.<br/>";
        }
    }

    echo "Memberships imported successfully.<br/>";
    $stmt->close();
}

function importOrders($mysqli, $ordersData) {
    $stmt = $mysqli->prepare("INSERT IGNORE INTO orders (orderID, customerID, trackingID, orderDateTime) VALUES (?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    foreach ($ordersData['orders'] as $order) {
        $stmt->bind_param("iiis",
            $order['OrderID'],
            $order['CustomerID'],
            $order['TrackingID'],
            $order['DateTime']
        );

        if (!$stmt->execute()) {
            echo "Error importing order with ID {$order['OrderID']}: " . $stmt->error . "<br/>";
        } else {
            // echo "Order with ID {$order['OrderID']} imported successfully.<br/>";
        }
    }

    echo "Orders imported successfully.<br/>";
    $stmt->close();
}

function importReviews($mysqli, $reviewsData) {
    $stmt = $mysqli->prepare("INSERT IGNORE INTO reviews (reviewID, customerID, productID, rating, comment) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    foreach ($reviewsData['reviews'] as $review) {
        $stmt->bind_param("iiiis",
            $review['ReviewID'],
            $review['CustomerID'],
            $review['ProductID'],
            $review['Rating'],
            $review['Comment']
        );

        if (!$stmt->execute()) {
            echo "Error importing review with ID {$review['ReviewID']}: " . $stmt->error . "<br/>";
        } else {
            // echo "Review with ID {$review['ReviewID']} imported successfully.<br/>";
        }
    }

    echo "Reviews imported successfully.<br/>";
    $stmt->close();
}

function importPromoCodes($mysqli, $promoCodesData) {
    // Prepare the SQL statement for inserting promo codes
    $stmt = $mysqli->prepare("INSERT IGNORE INTO promoCodes (promoCode, source, totalAvailable, isMemberOnly, expiration, discountType, discountAmount, restrictionAmount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    // Iterate through each promo code entry and bind values to the statement
    foreach ($promoCodesData['promoCodes'] as $code) {
        $isMemberOnly = $code['isMemberOnly'] ? 1 : 0; // Convert boolean to integer for MySQL
        $stmt->bind_param("ssisdsdd",
            $code['PromoCode'],
            $code['Source'],
            $code['TotalAvailable'],
            $isMemberOnly,
            $code['Expiration'],
            $code['DiscountType'],
            $code['DiscountAmount'],
            $code['RestrictionAmount']
        );

        if (!$stmt->execute()) {
            echo "Error importing promo code '{$code['PromoCode']}': " . $stmt->error . "<br/>";
        } else {
            // echo "Promo code '{$code['PromoCode']}' imported successfully.<br/>";
        }
    }

    echo "Promo codes imported successfully.<br/>";
    $stmt->close();
}

echo "<h3>Populating Database</h3>";
$productsData = readJson('hm_product_list.json');
$mockData = readJson('mock_data.json');

importProducts($mysqli, $productsData);
importProductCategories($mysqli, $productsData);
importColors($mysqli, $productsData);
importPrices($mysqli, $productsData);
importCustomers($mysqli, $mockData);
importMemberships($mysqli, $mockData);
importOrders($mysqli, $mockData);
importReviews($mysqli, $mockData);

// Close the connection
$mysqli->close();
?>