<?php include 'includes/header.php'; ?>
<?php
// include
require 'includes/ViewDB.php';
// Database credentials
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
?>

<h1>Product Features</h1>

You are now using the <b><?php echo $dbType; ?></b> database. Choose an option below: <br/><br/>
<a href="products.php" class="initbutton buttonBlue">Production Data</a>
<a href="products.php?data=sample" class="initbutton buttonOrange">Sample Data</a>
<br/><br/>

<style>
    h2 {
        text-align: center;
        color: #343a40;
    }
    form {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    label {
        flex: 1 1 45%;
        margin-bottom: 20px;
    }
    input[type="text"],
    input[type="number"],
    select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    button {
        background-color: #007bff;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 16px;
        flex: 1 1 100%;
        max-width: 200px;
        margin: 20px auto 0;
        display: block;
    }
    button:hover {
        background-color: #0056b3;
    }
    .result {
        margin-top: 30px;
    }
    table {
        border-collapse: collapse;
        margin: 0 auto; 
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        width: 100%;
    }
</style>
<div>
    <h2>Search Products</h2>
    <form method="post">
        <label>Product Name: <input type="text" name="name"></label><br>
        <label>Product ID: <input type="number" name="id"></label><br>
        <label>Attribute:
            <select name="attribute">
                <option value="all_attribute">All</option>
                <option value="New Arrival">New Arrival</option>
            </select>
        </label><br>
        <label>Availability:
            <select name="availability">
                <option value="all_availability">All</option>
                <option value="Available">Available</option>
                <option value="NotAvailable">Out of stock</option>
            </select>
        </label><br>
        <label>Main Category:
            <select name="mainCategory">
                <option value="all_mainCategory">All</option>
                <option value="ladies">Ladies</option>
                <option value="men">Men</option>
                <option value="kids">Kids</option>
                <option value="home">Home</option>
                <option value="sportswear">Sportswear</option>
            </select>
        </label><br>
        <label>Subcategories (comma separated): <input type="text" name="subcategories"></label><br>
        <button type="submit" name="submit_search">Search</button>
    </form><br/>

    <?php
    // Script to procces the above search form
    if (isset($_POST['submit_search'])) {
        // Retrieve form data and store in arrys
        $name = $_POST['name'] ?? '';
        $id = $_POST['id'] ?? '';
        $attribute = $_POST['attribute'] ?? 'all_attribute';
        $availability = $_POST['availability'] ?? 'all_availability';
        $mainCategory = $_POST['mainCategory'] ?? 'all_mainCategory';
        $subcategories = $_POST['subcategories'] ?? '';

        // SQL: constructing query to search
        $query0 = "
            SELECT 
                p.productID, 
                p.productName, 
                p.sellingAttribute, 
                p.stock,
                MAX(CASE WHEN pc.categoryType = 0 THEN pc.category ELSE NULL END) AS mainCategory
            FROM 
                products p 
        ";

        $query1 = "LEFT JOIN productCategories pc ON p.productID = pc.productID ";
        $query2 = "WHERE 1=1";
        $query3 = "";

        // Parameters array: The types array signify each type of input type for the later prepared statement to prevent sql injection
        $params = [];
        $types = '';

        // Add conditions based on input to build query
        if (!empty($name)) {
            $query2 .= " AND p.productName LIKE ?";
            $params[] = '%' . $name . '%';
            $types .= 's';
        }
        if (!empty($id)) {
            $query2 .= " AND p.productID LIKE ?";
            $params[] = '%' . $id . '%';
            $types .= 's';
        }
        if ($attribute != 'all_attribute') {
            $query2 .= " AND p.sellingAttribute = ?";
            $params[] = $attribute;
            $types .= 's';
        }
        if ($availability != 'all_availability') {
            $query2 .= " AND p.stock = ?";
            $params[] = $availability;
            $types .= 's';
        }
        if ($mainCategory != 'all_mainCategory') {
            $query2 .= " AND pc.category = ? AND pc.categoryType = 0";
            $params[] = $mainCategory;
            $types .= 's';
        }
        if (!empty($subcategories)) {
            $subcategoriesArray = array_map('trim', explode(',', $subcategories));
            $placeholders = implode(',', array_fill(0, count($subcategoriesArray), '?'));
            $query3 = " AND p.productID IN (
                SELECT productID 
                FROM productCategories 
                WHERE category IN ($placeholders) AND categoryType > 0
            )";
            foreach ($subcategoriesArray as $subcategory) {
                $params[] = $subcategory;
                $types .= 's';
            }
        }

        $query4 = " GROUP BY p.productID, p.productName, p.sellingAttribute, p.stock";

        // Combine queries, display individual sections on front end
        $query = $query0 . $query1 . $query2;
        if ($query3 != "") {
            $query .= $query3;
            $query .= $query4;
            echo "<big><code>$query0<br/>$query1<br/>$query2<br/>$query3<br/>$query4;</code></big><br/><br/>";
        } else {
            $query .= $query4;
            echo "<big><code>$query0<br/>$query1<br/>$query2<br/>$query4;</code></big><br/><br/>";
        }

        // Prepare statement, bind input parameters to specified input type to prevent injection
        $stmt = $conn->prepare($query);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        
        // Execute the statement
        $stmt->execute();
        $result = $stmt->get_result();

        // Display the table of products.         
        $tableDisplay = new ViewDB($conn);
        if ($dbType == 'production'){
            $tableDisplay->listProducts($result, FALSE);
        } else {
            $tableDisplay->listProducts($result, TRUE);
        }

        $stmt->close();
    }
    $conn->close();

    // For later use: promo code validator
    function isPromoCodeValid($discountType, $restrictionAmount, $orderTotal){
        if ($discountType === 'amount_off'){
            return $orderTotal > $restrictionAmount;
        }
        if ($discountType === 'percent_off'){
            return $orderTotal < $restrictionAmount;
        }
    }
?>
</div>

<?php include 'includes/footer.php'; ?>