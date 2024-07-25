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
<?php
// Database connection details
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "hmdatabase"; // Adjust as necessary

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop existing 'sales' temporary table
$sqlDropSales = "DROP TEMPORARY TABLE IF EXISTS sales;";
if ($conn->query($sqlDropSales) === TRUE) {

} else {
    echo "Error dropping 'sales' table: " . $conn->error . "<br>";
}

// Create 'sales' temporary table
$sqlCreateSales = <<<SQL
CREATE TEMPORARY TABLE sales AS
SELECT p.productID, 
       SUM(COALESCE(od.count, 0)) AS numSold,
       CAST(SUM(COALESCE(od.count, 0)) * 1.0 / MAX(SUM(COALESCE(od.count, 0))) OVER () AS DECIMAL(10, 5)) AS normNumSold
FROM products p
LEFT JOIN orderDetails od ON p.productID = od.productID
GROUP BY p.productID;
SQL;

if ($conn->query($sqlCreateSales) === TRUE) {

} else {
    echo "Error creating 'sales' table: " . $conn->error . "<br>";
}

// Drop existing 'ratings' temporary table
$sqlDropRatings = "DROP TEMPORARY TABLE IF EXISTS ratings;";
if ($conn->query($sqlDropRatings) === TRUE) {

} else {
    echo "Error dropping 'ratings' table: " . $conn->error . "<br>";
}

// Create 'ratings' temporary table
$sqlCreateRatings = <<<SQL
CREATE TEMPORARY TABLE ratings AS
SELECT p.productID, 
       SUM(COALESCE(r.rating, 0)) AS totrat, 
       COUNT(r.productID) AS numRat,
       COALESCE(SUM(COALESCE(r.rating, 0)) * 1.0 / NULLIF(COUNT(r.productID), 0), 0) AS aveRating,
       CAST(COALESCE(SUM(COALESCE(r.rating, 0)) * 1.0 / NULLIF(COUNT(r.productID), 0) / MAX(SUM(COALESCE(r.rating, 0)) * 1.0 / NULLIF(COUNT(r.productID), 0)) OVER (), 0) AS DECIMAL(10, 5)) AS normAveRating
FROM products p 
LEFT JOIN reviews r ON p.productID = r.productID
GROUP BY p.productID;
SQL;

if ($conn->query($sqlCreateRatings) === TRUE) {

} else {
    echo "Error creating 'ratings' table: " . $conn->error . "<br>";
}

// Drop the #returnrates table if it exists
$sqlDropReturns = "DROP TEMPORARY TABLE IF EXISTS returnrates;";
if ($conn->query($sqlDropReturns) === TRUE) {

} else {
    echo "Error dropping table 'returnrates': " . $conn->error . "<br>";
}

// Create the #returnrates table
$sqlCreateReturns = "
    CREATE TEMPORARY TABLE returnrates AS
    SELECT products.productID, 
           COALESCE(SUM(returns.numReturned), 0) AS numReturned, 
           COALESCE(tot.totalReturned, 0) AS totReturn,
           COALESCE(CAST(SUM(returns.numReturned) * 1.0 / NULLIF(tot.totalReturned, 0) AS DECIMAL(5,4)), 0.0000) AS returnRate,
           CAST(COALESCE(SUM(returns.numReturned) * 1.0 / NULLIF(tot.totalReturned, 0) / MAX(SUM(returns.numReturned) * 1.0 / NULLIF(tot.totalReturned, 0)) OVER (), 0) AS DECIMAL(10,5)) AS normReturnRate
    FROM products
    LEFT JOIN (
        SELECT orderDetails.productID, 
               COALESCE(SUM(orderDetails.count), 0) AS numReturned
        FROM orderDetails
        LEFT JOIN (
            SELECT orders.orderID
            FROM orders
            LEFT JOIN returnedOrders ON orders.orderID = returnedOrders.orderID
            GROUP BY orders.orderID
            HAVING COUNT(returnedOrders.orderID) > 0
        ) AS returned ON returned.orderID = orderDetails.orderID
        GROUP BY orderDetails.productID
    ) AS returns ON products.productID = returns.productID
    CROSS JOIN (
        -- Total number of items returned
        SELECT COALESCE(SUM(numReturned), 0) AS totalReturned
        FROM (
            SELECT COALESCE(SUM(orderDetails.count), 0) AS numReturned
            FROM orderDetails
            LEFT JOIN (
                SELECT orders.orderID
                FROM orders
                LEFT JOIN returnedOrders ON orders.orderID = returnedOrders.orderID
                GROUP BY orders.orderID
                HAVING COUNT(returnedOrders.orderID) > 0
            ) AS returned ON returned.orderID = orderDetails.orderID
            GROUP BY orderDetails.productID
        ) AS returnedProducts
    ) AS tot
    GROUP BY products.productID, tot.totalReturned;
";

if ($conn->query($sqlCreateReturns) === TRUE) {
    
} else {
    echo "Error creating table 'returnrates': " . $conn->error . "<br>";
}

// Drop the #defaultrank table if it exists
// $sqlDropRanks = "DROP TEMPORARY TABLE IF EXISTS defaultrank;";
// if ($conn->query($sqlDropRanks) === TRUE) {
//     echo "Temporary table 'defaultrank' dropped if it existed.<br>";
// } else {
//     echo "Error dropping table 'defaultrank': " . $conn->error . "<br>";
// }

// Create the #defaultrank table
// $sqlCreateRanks = "
//     CREATE TEMPORARY TABLE defaultrank AS
//     SELECT 
//         p.productID,
//         CAST((0.5 * COALESCE(s.normNumSold, 0) + 
//             0.3 * COALESCE(r.normAveRating, 0) + 
//             0.2 * COALESCE(1-ret.normReturnRate, 0)) AS DECIMAL(10,5)) AS Score
//     FROM products p
//     LEFT JOIN sales s ON s.productID = p.productID
//     LEFT JOIN ratings r ON r.productID = p.productID
//     LEFT JOIN returnrates ret ON ret.productID = p.productID
//     ORDER BY Score DESC;
// ";

// if ($conn->query($sqlCreateRanks) === TRUE) {
//     echo "Temporary table 'defaultrank' created successfully.<br>";
// } else {
//     echo "Error creating table 'defaultrank': " . $conn->error . "<br>";
// }

// Drop the #purchase table if it exists
$sqlDropPurchases = "DROP TEMPORARY TABLE IF EXISTS purchase;";
if ($conn->query($sqlDropPurchases) === TRUE) {
    
} else {
    echo "Error dropping table 'purchase': " . $conn->error . "<br>";
}

// Create the #purchase table
$sqlCreatePurchases = "
    CREATE TEMPORARY TABLE purchase AS
    SELECT 
        customerID,
        productID,
        category,
        categorytype,
        color,
        CustomerScore
    FROM (
        SELECT 
            c.customerID,
            p.productID, 
            cat.category, 
            cat.categorytype, 
            IF(LOCATE('/', col.colorName) > 0, 
            LEFT(col.colorName, LOCATE('/', col.colorName) - 1),
            col.colorName) AS color,
            CASE WHEN EXISTS (
                    SELECT 1
                    FROM orders o
                    JOIN orderDetails od ON o.orderID = od.orderID
                    JOIN products p2 ON od.productID = p2.productID
                    JOIN productCategories cat2 ON p2.productID = cat2.productID
                    WHERE o.customerID = c.customerID
                    AND EXISTS (
                        SELECT 1
                        FROM productCategories cat3
                        WHERE cat3.productID = p.productID
                            AND (cat3.category = cat2.category OR cat3.categorytype = cat2.categorytype)
                    )
                )
                THEN 0.4 * COALESCE(s.normNumSold, 0) + 
                    0.3 * COALESCE(r.normAveRating, 0) + 
                    0.2 * COALESCE(1 - ret.normReturnRate, 0) + 
                    0.1 -- Adjusted score if match found
                ELSE 0.5 * COALESCE(s.normNumSold, 0) + 
                    0.3 * COALESCE(r.normAveRating, 0) + 
                    0.2 * COALESCE(1 - ret.normReturnRate, 0) -- Default score
            END AS CustomerScore
        FROM products p 
        LEFT JOIN orderDetails od ON od.productID = p.productID
        LEFT JOIN orders o ON o.orderID = od.orderID
        LEFT JOIN customers c ON c.customerID = o.customerID
        LEFT JOIN productCategories cat ON cat.productID = p.productID
        LEFT JOIN productColors col ON col.articleID = p.productID
        LEFT JOIN sales s ON s.productID = p.productID
        LEFT JOIN ratings r ON r.productID = p.productID
        LEFT JOIN returnrates ret ON ret.productID = p.productID
        WHERE c.customerID = {$userID}
    ) AS subquery
    ORDER BY CustomerScore DESC;
";

if ($conn->query($sqlCreatePurchases) === TRUE) {
    
} else {
    echo "Error creating table 'purchase': " . $conn->error . "<br>";
}

$sqlDropFinalRanking = "DROP TABLE IF EXISTS ranking";
if ($conn->query($sqlDropFinalRanking) === TRUE) {
    
} else {
    echo "Error dropping table 'ranking': " . $conn->error . "<br>";
}

$sqlFinalRanking = "
    CREATE TEMPORARY TABLE ranking AS
    SELECT DISTINCT
        inner_query.productID,
        inner_query.productName
    FROM (
        SELECT 
            p.productID,
            p.productName,
            pc.CustomerScore
        FROM 
            purchase pc
        LEFT JOIN 
            (SELECT productID, productName FROM products) p 
        ON 
            p.productID = pc.productID
        ORDER BY 
            pc.CustomerScore DESC
    ) AS inner_query;
";

if ($conn->query($sqlFinalRanking) === TRUE) {
    
} else {
    echo "Error creating table 'ranking': " . $conn->error . "<br>";
}

?>
<style>
    table {
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f4f4f4;
    }
    section {
        margin-bottom: 20px;
    }
</style>
<?php
// Query the ranking table
$sqlRanking = "SELECT productID, productName FROM ranking LIMIT 10";
$result = $conn->query($sqlRanking);

if ($result->num_rows > 0) {
    // Start the HTML output
    echo '<section>';
    echo "Based on your purchase history.";
    echo '<table style="width:50%;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Product ID</th>';
    echo '<th>Product Name</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch and display the results
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td><a href="product.php?id=' . $row['productID'] . '">' . $row['productID'] . '</a></td>';
        echo '<td>' . $row['productName'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</section>';
} else {
    echo 'No recomendations based on your purchase history.';
}
// Close the connection
$conn->close();
?>