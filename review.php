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

echo "<h1>Edit or Delete a Review</h1>";

if (isset($_GET['id'])) {
    $reviewId = $_GET['id'];
    $review = null;
    $customerID = 100000; //hardcode

    $sql = "SELECT * FROM reviews WHERE reviewID = '$reviewId'";
    $result = mysqli_query($conn, $sql);    
    if ($result && mysqli_num_rows($result) > 0) {
        $review = mysqli_fetch_assoc($result);
    }

    if ($review) {
        $productId = $review['productID'];
        $rating = $review['rating'];
        $comment = $review['comment'];
        
        echo "<h2>" . $reviewId . "</h2>";
        echo "Product: <a href='../product.php?id={$productId}'>{$productId}</a><br/>";
        echo "Made by user: " . $review['customerID'];
        echo "<br/>Rating: ";
        $stars = $rating;
        for ($star = 1; $star <= $stars; $star++){
            echo "★";
        }
        for ($blankStar = $stars + 1; $blankStar <= 5; $blankStar++){
            echo "☆";
        }
        echo "<br/>Comment: " . $comment;
    } else {
        echo "Review not found";
    }

} else {
    echo "Review ID is required";
}
?>

<?php if ($review && $review['customerID'] == $customerID): ?>
<br/><br/>
<b>Edit Review</b>
<form method="post">
    <label>
        <select name="stars">
            <option value="" disabled selected>Select a Rating:</option>
            <option value="1">★☆☆☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="3">★★★☆☆</option>
            <option value="4">★★★★☆</option>
            <option value="5">★★★★★</option>
        </select>
    </label><br/>
    <textarea name="comment" placeholder="Leave a comment..." style="width: 100%;"><?php echo $review['comment']; ?></textarea><br/>
    <button type="submit" name="submit_edit">Save Edits</button>
</form>

<?php
// submit review
if (isset($_POST['submit_edit'])) {
    // Retrieve form data
    if (isset($_POST['stars'])){
        $rating = $_POST['stars'];
        $comment = $_POST['comment'];

        // SQL: update review
        $stmt = $conn->prepare("UPDATE reviews SET customerID = ?, productID = ?, rating = ?, comment = ? WHERE reviewID = ?");
        $stmt->bind_param("iiisi", 
            $customerID,
            $productId,
            $rating,
            $comment,
            $reviewId
        );
        if (!$stmt->execute()) {
            echo "Error saving edits: " . $stmt->error . "<br/>";
        } else {
            header("Location: product.php?id=" . $productId);
            exit();
        }
    } else {
        echo "Please select a rating.<br/>";
    }
}
?>
<br/>
<form method="post">
    <button type="submit" name="delete_review">Delete Review</button>
</form>

<?php
// delete review
if (isset($_POST['delete_review'])) {
    // SQL: delete review
    $stmt = $conn->prepare("DELETE FROM reviews WHERE reviewID = ?");
    $stmt->bind_param("i", $reviewId);
    if (!$stmt->execute()) {
        echo "Error deleting review: " . $stmt->error . "<br/>";
    } else {
        header("Location: product.php?id=" . $productId);
        exit();
    }
}
?>

<?php else: ?>
<br/><br/>You cannot edit or delete this review.
<?php endif; ?>

<?php include 'includes/footer.php'; ?>