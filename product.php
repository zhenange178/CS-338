<?php include 'includes/header.php'; ?>
<?php
$servername = "127.0.0.1";
$username = "user1";
$password = "password";
$dbname = "sampledatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $customerID = 100000; // hardcoded

    $product = null;
    $categories = [];
    $colors = [];
    $prices = [];
    $reviews = [];
    $myReviews = [];

    // SQL: get product table
    $sqlProduct = "SELECT * FROM products WHERE productID = '$productId'";
    $resultProduct = mysqli_query($conn, $sqlProduct);    
    if ($resultProduct && mysqli_num_rows($resultProduct) > 0) {
        $product = mysqli_fetch_assoc($resultProduct);
    }

    // SQL: get product categories table
    $sqlCategories = "SELECT * FROM productCategories WHERE productID = '$productId'";
    $resultCategories = mysqli_query($conn, $sqlCategories);
    while ($row = $resultCategories->fetch_assoc()) {
        $categories[] = $row;
    }
    
    // SQL: get product colors table
    $sqlColors = "SELECT * FROM productColors WHERE productID = '$productId'";
    $resultColors = mysqli_query($conn, $sqlColors);
    while ($row = $resultColors->fetch_assoc()) {
        $colors[] = $row;
    }

    // SQL: get product prices table
    $sqlPrices = "SELECT * FROM productPrices WHERE productID = '$productId'";
    $resultPrices = mysqli_query($conn, $sqlPrices);
    while ($row = $resultPrices->fetch_assoc()) {
        $prices[] = $row;
    }

    // SQL: get my reviews table
    $sqlMyReviews = "SELECT * FROM reviews WHERE productID = '$productId' AND customerID = '$customerID'";
    $resultMyReviews = mysqli_query($conn, $sqlMyReviews);
    while ($row = $resultMyReviews->fetch_assoc()) {
        $myReviews[] = $row;
    }
    if ($myReviews){
        $myReviews = array_reverse($myReviews); // newest first
    }

    // SQL: get other reviews table
    $sqlReviews = "SELECT * FROM reviews WHERE productID = '$productId' AND customerID != '$customerID'";
    $resultReviews = mysqli_query($conn, $sqlReviews);
    while ($row = $resultReviews->fetch_assoc()) {
        $reviews[] = $row;
    }
    if ($reviews){
        $reviews = array_reverse($reviews); // newest first
    }

    if ($product) {
        echo "<br/>";
    } else {
        echo "Product not found";
    }
} else {
    echo "Product ID is required";
}

// function sqlToVar($conn, $sql) {
//     $stmt = $conn->prepare($sql);
//     $stmt->execute();
//     $stmt->bind_result($output);
//     $stmt->fetch();
//     $stmt->close();
//     return $output;
// }
?>
<?php if ($product): ?>
<div class="container" style="display: flex;">
    <div style="flex: 1;">
        <img src="<?php echo $product['productImage']; ?>" alt="Product Image" style="max-width: 100%; height: auto; max-height: 500px; display: block; margin: 0px auto;">
    </div>
    <div style="flex: 1; padding: 10px;">
        <div style="text-align:center; background-color: #f0f0f0; padding: 0 5px 5px;">
            <h2><?php echo "<h2>{$product['productName']}"; ?></h2>
            <big><?php 
            $whitePrice = null;
            $redPrice = null;
            foreach ($prices as $price) {
                if ($price['priceType'] === 'whitePrice') {
                    $whitePrice = $price['price'];
                }
                if ($price['priceType'] === 'redPrice') {
                    $redPrice = $price['price'];
                }
            }
            if ($whitePrice !== null) {
                if ($redPrice != null){
                    echo "<s>$" . $whitePrice . "</s> $" . $redPrice;
                } else {
                    echo "$" . $whitePrice;
                }
            } else {
                echo "Price not found";
            }
            ?></big>
        </div>
        <div style="padding: 10px 5px 5px;">
            <?php 
            if ($product['sellingAttribute'] === 'New Arrival'){
                echo "<big>New Arrival</big><br/>";
            }
            if ($product['stock'] === 'Available'){
                echo "In Stock<br/>";
            } else {
                echo "Out of Stock<br/>";
            }
            echo "<br /><big>Category: " . $categories[0]['category'] . "</big><br />";
            echo "Tags: ";
            foreach ($categories as $category){
                echo $category['category'] . ", ";
            }
            echo "<br/><br/><big>Colors:</big>";
            ?>
            <div class="container" style="display: flex; flex-wrap: wrap;">
                <?php
                foreach ($colors as $color){
                    echo '<div >';
                    $image = $color['articleImage'];
                    echo '<img src="' . $color['articleImage'] . '" alt="' . $color['colorName'] . '" style="max-width: 100px; height: auto; max-height: 80px; display: block; margin: 0px auto;">';
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        <!-- Categories, colors, and other information to follow. -->
    </div>
</div>

<h2>Reviews (<?php echo (count($reviews) + count($myReviews))?>)</h2>
<form method="post">
    <label>Leave a review:
        <select name="stars">
            <option value="" disabled selected>Select a Rating:</option>
            <option value="1">★☆☆☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="3">★★★☆☆</option>
            <option value="4">★★★★☆</option>
            <option value="5">★★★★★</option>
        </select>
    </label><br/>
    <textarea name="comment" placeholder="Leave a comment..." style="width: 100%;"></textarea><br/>
    <!-- <textarea name="comment" placeholder="Leave a comment..." style="width: 100%;
            font-size:16px;
            height: auto; 
            min-height: 5em; 
            box-sizing: border-box; 
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            overflow-wrap: break-word;
            word-wrap: break-word;"></textarea> -->
    <button type="submit" name="submit_review">Submit</button>
</form>

<?php
// submit review
if (isset($_POST['submit_review'])) {
    // Retrieve form data
    if (isset($_POST['stars'])){
        $rating = $_POST['stars'];
        $comment = $_POST['comment'];

        // SQL: insert review
        $stmt = $conn->prepare("INSERT INTO reviews (customerID, productID, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", 
            $customerID,
            $productId,
            $rating,
            $comment
        );
        if (!$stmt->execute()) {
            echo "Error adding review: " . $stmt->error . "<br/>";
        } else {
            header("Location: product.php?id=" . $productId);
            exit();
        }
    } else {
        echo "Please select a rating.<br/>";
    }
}

// Reviews list
echo "<br/>";
// My Reviews first
foreach ($myReviews as $review) {
    // stars
    echo "<big>Me</big><br/>";
    $stars = $review['rating'];
    for ($star = 1; $star <= $stars; $star++){
        echo "★";
    }
    for ($blankStar = $stars + 1; $blankStar <= 5; $blankStar++){
        echo "☆";
    }
    echo " <a href='../review.php?id={$review["reviewID"]}'>edit</a>";
    if ($review['comment']){
        echo "<br/>";
    }
    echo $review['comment'] . "<br/><br/><br/>";
}


foreach ($reviews as $review) {
    // stars
    echo "<big>User " . $review['customerID'] . "</big><br/>";
    $stars = $review['rating'];
    for ($star = 1; $star <= $stars; $star++){
        echo "★";
    }
    for ($blankStar = $stars + 1; $blankStar <= 5; $blankStar++){
        echo "☆";
    }
    if ($review['comment']){
        echo "<br/>";
    }
    echo $review['comment'] . "<br/><br/><br/>";
}
?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>