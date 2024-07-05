<?php
class DatabasePopulator {
    /**
     * readJson
     * read a json file, return an encoded list
     * 
     * @param string $filename path to json file
     * @return JSON object
     */
    public function readJson($filename) {
        $jsonContent = file_get_contents($filename);
        return json_decode($jsonContent, true);
    }

    // Import data functions
    public function importProducts($mysqli, $productsData) {
        // Prepare the statements for each table
        $productStmt = $mysqli->prepare("INSERT IGNORE INTO products (productID, productName, productURL, productImage, productWeight, sellingAttribute, stock, comingSoon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $categoryStmt = $mysqli->prepare("INSERT IGNORE INTO productCategories (productID, category, categoryType) VALUES (?, ?, ?)");
        $colorStmt = $mysqli->prepare("INSERT IGNORE INTO productColors (articleID, productID, colorName, colorCode, articleImage) VALUES (?, ?, ?, ?, ?)");
        $priceStmt = $mysqli->prepare("INSERT IGNORE INTO productPrices (productID, priceType, price) VALUES (?, ?, ?)");
        
        // Check for errors in statement preparation
        if ($productStmt === false || $categoryStmt === false || $colorStmt === false || $priceStmt === false) {
            die('MySQL prepare error: ' . $mysqli->error);
        }

        foreach ($productsData['products'] as $product) {
            // Import products
            $success = true;
            $url = 'https://www2.hm.com/' . $product['url'];
            $productStmt->bind_param("isssdssi",
                $product['id'],
                $product['productName'],
                $url,
                $product['modelImage'],
                $product['quantity'],
                $product['sellingAttribute'],
                $product['availability']['stockState'],
                $product['availability']['comingSoon']
            );
            if(!$productStmt->execute()){
                $success = false;
            }

            // Import product categories
            if (isset($product['mainCatCode'])) {
                $categories = explode('_', $product['mainCatCode']);
                for ($i = 0; $i < count($categories); $i++) {
                    $categoryStmt->bind_param("isi",
                        $product['id'],
                        $categories[$i],
                        $i
                    );
                    if(!$categoryStmt->execute()){
                        $success = false;
                    }
                }
            }

            // Import colors
            if (isset($product['swatches'])) {
                foreach ($product['swatches'] as $color) {
                    $colorStmt->bind_param("iisss",
                        $color['articleId'],
                        $product['id'],
                        $color['colorName'],
                        $color['colorCode'],
                        $color['productImage']
                    );
                    if(!$colorStmt->execute()){
                        $success = false;
                    }
                }
            }

            // Import prices
            if (isset($product['prices'])) {
                foreach ($product['prices'] as $price) {
                    $priceStmt->bind_param("isd",
                        $product['id'],
                        $price['priceType'],
                        $price['price']
                    );
                    if(!$priceStmt->execute()){
                        $success = false;
                    }
                }
            }

            if (!$success) {
                echo "Error importing product with ID {$product['id']}: " . $stmt->error . "<br/>";
            } else {
                // echo "Customer with ID {$customer['ID']} imported successfully.<br/>";
            }
        }

        // echo "Product data imported successfully.<br/>";
        $productStmt->close();
        $categoryStmt->close();
        $colorStmt->close();
        $priceStmt->close();
    }

    public function importCustomers($mysqli, $customersData) {
        $realUserStmt = $mysqli->prepare("INSERT IGNORE INTO customers (customerID) VALUES (100000)");
        if (!$realUserStmt->execute()) {
            echo "Error importing customer 0<br/>";
        }

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

        //echo "Customers imported successfully.<br/>";
        $stmt->close();
    }

    public function importMemberships($mysqli, $membershipsData) {
        $stmt = $mysqli->prepare("INSERT IGNORE INTO memberships (membershipID, customerID, membershipPrice, memberRank, expirationDate) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die('MySQL prepare error: ' . $mysqli->error);
        }

        foreach ($membershipsData['memberships'] as $membership) {
            $stmt->bind_param("iidis",
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

        //echo "Memberships imported successfully.<br/>";
        $stmt->close();
    }

    public function importOrders($mysqli, $ordersData) {
        $ordersStmt = $mysqli->prepare("INSERT IGNORE INTO orders (orderID, customerID, trackingID, orderDateTime, promoCodeUsed) VALUES (?, ?, ?, ?, ?)");
        $returnsStmt = $mysqli->prepare("INSERT IGNORE INTO returnedOrders (orderID, returnDateTime, returnReason) VALUES (?, ?, ?)");
        $detailsStmt = $mysqli->prepare("INSERT IGNORE INTO orderDetails (orderID, productID, count) VALUES (?, ?, ?)");
        
        if ($ordersStmt === false || $returnsStmt === false || $detailsStmt === false) {
            die('MySQL prepare error: ' . $mysqli->error);
        }

        // import orders
        foreach ($ordersData['orders'] as $order) {
            $success = true;
            $orderID = $order['OrderID'];
            $promoCode = null;
            
            // add promo code
            if (isset($order['PromoCode'])) {
                $promoCode = $order['PromoCode'];
            }
            
            // orders
            $ordersStmt->bind_param("iiiss",
                $orderID,
                $order['CustomerID'],
                $order['TrackingID'],
                $order['DateTime'],
                $promoCode
            );
            if (!$ordersStmt->execute()){
                $success = false;
            }

            // Add details
            if (isset($order['Products'])) {
                foreach ($order['Products'] as $item) {
                    $detailsStmt->bind_param("iii",
                        $orderID,
                        $item['ProductID'],
                        $item['Count'],
                    );
                    if(!$detailsStmt->execute()){
                        $success = false;
                    }
                }
            }
            
            // Check if the order has a 'returned' status
            if (isset($order['returned'])) {
                $returnsStmt->bind_param(
                    'iss',
                    $orderID,
                    $order['returned']['returnDateTime'],
                    $order['returned']['returnReason']
                );
                if (!$returnsStmt->execute()) {
                    $success = false;
                }
            }

            if (!$success) {
                echo "Error importing order with ID {$order['OrderID']}: " . $ordersStmt->error . "<br/>";
            } else {
                // echo "Order with ID {$order['OrderID']} imported successfully.<br/>";
            }
        }

        //echo "Orders imported successfully.<br/>";
        $ordersStmt->close();
        $returnsStmt->close();
        $detailsStmt->close();
    }


    public function importReviews($mysqli, $reviewsData) {
        $stmt = $mysqli->prepare("INSERT IGNORE INTO reviews (reviewID, customerID, productID, rating, comment) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die('MySQL prepare error: ' . $mysqli->error);
        }

        foreach ($reviewsData['reviews'] as $review) {
            // attempt to fix not loading
            $productID = (int)$review['ProductID'];
            $stmt->bind_param("iiiis",
                $review['ReviewID'],
                $review['CustomerID'],
                $productID,
                $review['Rating'],
                $review['Comment']
            );

            if (!$stmt->execute()) {
                echo "Error importing review with ID {$review['ReviewID']}: " . $stmt->error . "<br/>";
            } else {
                // echo "Review with ID {$review['ReviewID']} imported successfully.<br/>";
            }
        }

        //echo "Reviews imported successfully.<br/>";
        $stmt->close();
    }

    public function importPromoCodes($mysqli, $promoCodesData) {
        // Prepare the SQL statement for inserting promo codes
        $stmt = $mysqli->prepare("INSERT IGNORE INTO promoCodes (promoCode, source, totalAvailable, isMemberOnly, expiration, discountType, discountAmount, restrictionAmount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die('MySQL prepare error: ' . $mysqli->error);
        }

        // Iterate through each promo code entry and bind values to the statement
        foreach ($promoCodesData['promoCodes'] as $code) {
            $isMemberOnly = $code['isMemberOnly'] ? 1 : 0; // Convert boolean to integer for MySQL
            $stmt->bind_param("ssiissdd",
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

        //echo "Promo codes imported successfully.<br/>";
        $stmt->close();
    }
}
?>