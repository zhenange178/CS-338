<?php
class DatabaseCreator{
    public function createDatabase($mysqli, $dbname){
        $startTime = microtime(true);
        echo "<b>Initializing Database...</b><br />";

        // Check if exists, drop if needed, create. 
        $result = $mysqli->query("SHOW DATABASES LIKE '$dbname'");
        $exists = $result->num_rows > 0;

        if ($exists) {
            if ($mysqli->query("DROP DATABASE $dbname") === TRUE) {
                echo "Old database dropped<br/>";
            } else {
                echo "Error dropping database: " . $mysqli->error . "<br/>";
            }
        }

        if ($mysqli->query("CREATE DATABASE $dbname") === TRUE) {
            echo "Database created: '" . $dbname . "'<br />";
        } else {
            echo "Error creating database: " . $mysqli->error;
        }

        $mysqli->select_db($dbname);

        /**
         * Create Tables
         */

        $tablesCreated = true;
        echo "Creating tables...<br/>";

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
            // echo "Table 'promoCodes' created<br />";
        } else {
            echo "Error creating table 'promoCodes': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create Product Table
        $sql = "CREATE TABLE IF NOT EXISTS products (
            productID INT PRIMARY KEY,
            productName VARCHAR(255) NOT NULL,
            productURL VARCHAR(255) NOT NULL,
            productImage VARCHAR(255) NOT NULL,
            productWeight FLOAT,
            sellingAttribute VARCHAR(255),
            stock VARCHAR(255) NOT NULL,
            comingSoon BOOLEAN NOT NULL
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'products' created<br />";
        } else {
            echo "Error creating table 'products': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        $sql = "CREATE TABLE IF NOT EXISTS productCategories (
            productID INT,
            category VARCHAR(255) NOT NULL,
            categoryType INT NOT NULL,
            CONSTRAINT fk_product_category
                FOREIGN KEY (productID) 
                REFERENCES products(productID)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'productCategories' created<br />";
        } else {
            echo "Error creating table 'productCategories': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create Product/color Table
        $sql = "CREATE TABLE IF NOT EXISTS productColors (
            articleID INT PRIMARY KEY,
            productID INT,
            colorName VARCHAR(255) NOT NULL,
            colorCode VARCHAR(255) NOT NULL,
            articleImage VARCHAR(255) NOT NULL,
            CONSTRAINT fk_product_color
                FOREIGN KEY (productID) 
                REFERENCES products(productID)
                ON DELETE SET NULL
                ON UPDATE CASCADE
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'productColors' created<br />";
        } else {
            echo "Error creating table 'productColors': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create Product/price Table
        $sql = "CREATE TABLE IF NOT EXISTS productPrices (
            productID INT,
            priceType VARCHAR(255) NOT NULL,
            price FLOAT NOT NULL,
            CONSTRAINT fk_product_price
                FOREIGN KEY (productID) 
                REFERENCES products(productID)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'productPrices' created<br />";
        } else {
            echo "Error creating table 'productPrices': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create Customer Table
        $sql = "CREATE TABLE IF NOT EXISTS customers (
            customerID INT PRIMARY KEY,
            firstName VARCHAR(255),
            lastName VARCHAR(255),
            customerEmail VARCHAR(255),
            customerPhone VARCHAR(255),
            customerAddress VARCHAR(255),
            customerBirth DATE
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'customers' created<br />";
        } else {
            echo "Error creating table 'customers': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create membership Table
        $sql = "CREATE TABLE IF NOT EXISTS memberships (
            membershipID INT AUTO_INCREMENT PRIMARY KEY,
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
            // echo "Table 'memberships' created<br />";
        } else {
            echo "Error creating table 'memberships': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create orders Table
        $sql = "CREATE TABLE IF NOT EXISTS orders (
            orderID INT AUTO_INCREMENT PRIMARY KEY,
            customerID INT,
            trackingID INT NOT NULL,
            orderDateTime DATETIME NOT NULL,
            promoCodeUsed VARCHAR(255),
            CONSTRAINT fk_customer_order
                FOREIGN KEY (customerID) 
                REFERENCES customers(customerID)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
            CONSTRAINT fk_code_order
                FOREIGN KEY (promoCodeUsed) 
                REFERENCES promoCodes(promoCode)
                ON DELETE SET NULL
                ON UPDATE CASCADE
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'orders' created<br />";
        } else {
            echo "Error creating table 'orders': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create returned orders Table
        $sql = "CREATE TABLE IF NOT EXISTS returnedOrders (
            orderID INT,
            returnDateTime DATETIME NOT NULL,
            returnReason VARCHAR(255),
            CONSTRAINT fk_order_return
                FOREIGN KEY (orderID) 
                REFERENCES orders(orderID)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'returnedOrders' created<br />";
        } else {
            echo "Error creating table 'returnedOrders': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create orderDetails Table
        $sql = "CREATE TABLE IF NOT EXISTS orderDetails (
            orderID INT,
            productID INT,
            count INT NOT NULL,
            CONSTRAINT fk_order_detail
                FOREIGN KEY (orderID) 
                REFERENCES orders(orderID)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            CONSTRAINT fk_product_detail
                FOREIGN KEY (productID) 
                REFERENCES productColors(articleID)
                ON DELETE SET NULL
                ON UPDATE CASCADE
        )";

        if ($mysqli->query($sql) === TRUE) {
            // echo "Table 'orderDetails' created<br />";
        } else {
            echo "Error creating table 'orderDetails': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // Create reviews Table
        $sql = "CREATE TABLE IF NOT EXISTS reviews (
            reviewID INT AUTO_INCREMENT PRIMARY KEY,
            customerID INT,
            productID INT,
            rating INT NOT NULL,
            comment VARCHAR(255),
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
            // echo "Table 'reviews' created<br />";
        } else {
            echo "Error creating table 'reviews': " . $mysqli->error . "<br />";
            $tablesCreated = false;
        }

        // finish
        if ($tablesCreated) {
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            echo "All tables created in " . number_format($executionTime, 2) . " seconds.<br /><br />";
        } else {
            echo "There were errors creating some tables.<br /><br />";
        }
    }
}
?>