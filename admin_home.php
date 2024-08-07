<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['userID']) || $_SESSION['userID'] != 999999) {
    header("Location: /");
    exit();
}
?>

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

<h1>Admin Center</h1>

You are now using the <b><?php echo $dbType; ?></b> database. Choose an option below: <br/><br/>
<a href="admin_home.php" class="initbutton buttonBlue">Production Data</a>
<a href="admin_home.php?data=sample" class="initbutton buttonOrange">Sample Data</a>
<br/><br/>


<div style="border: 1px solid black; padding: 5px; width: 50%;">
<b>Contents</b>
<ul>
    <li><a href="#promocode">Top 10 Promo Codes</a></li>
    <li><a href="#outofstock">Out of Stock Items</a></li>
    <li><a href="#bestcustomer">Most Popular Customer</a></li>
    <li><a href="#charts">Other Analytics</a></li>
</ul>
</div>

<section id="promocode">
<div>
<h2>Top 10 Most Used Promo Codes</h2>
<?php
// SQL: Select no null promo codes, sort by most used
$sql = "SELECT promoCodeUsed, COUNT(*) as orderCount FROM orders WHERE promoCodeUsed IS NOT NULL GROUP BY promoCodeUsed ORDER BY orderCount DESC LIMIT 10";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table style='border-collapse: collapse; width: 80%; border: 1px solid #ddd;'>";
    echo "<tr><th style='border: 1px solid #ddd; padding: 8px;'>Promo Code</th><th style='border: 1px solid #ddd; padding: 8px;'>Order Count</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>".$row["promoCodeUsed"]."</td><td style='border: 1px solid #ddd; padding: 8px;'>".$row["orderCount"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "No results found";
}
?>
<h3>Retrieve Promo Code Attributes</h3>
    <form method="post">
        <label for="promoCode">Enter Promo Code:</label>
        <input type="text" id="promoCode" name="promoCode" required>
        <br>
        <input type="submit" name="submit_search">
    </form>
</div>
<?php
if (isset($_POST['submit_search'])) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve promo code from form submission
    $promoCode = $_POST["promoCode"];

    // Prepare SQL statement to retrieve promo code attributes
    $sql = "SELECT discountType, discountAmount, restrictionAmount
            FROM PromoCodes
            WHERE promoCode = ?";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $promoCode);

    // Execute the query
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($discountType, $discountAmount, $restrictionAmount);

    // Fetch values
    if ($stmt->fetch()) {
        if ($discountType === 'amount_off'){
            echo "Promo code $promoCode: \$$discountAmount off orders over \$$restrictionAmount";
        } else{
            echo "Promo code $promoCode: $discountAmount% off orders under \$$restrictionAmount";
        }
    } else {
        echo "<p>Promo code '$promoCode' not found.</p>";
    }

    // Close statement and connection
    $stmt->close();
}
?>
<br/>
</section>

<section id="outofstock">
<h2>Out of Stock Items</h2>
<?php
// SQL: Select out of stock items
$sql = "SELECT productID FROM products WHERE Stock = 'NotAvailable'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table style='border-collapse: collapse; border: 1px solid #ddd;'>";
    echo "<tr><th style='border: 1px solid #ddd; padding: 8px;'>Product ID</th></tr>";
    while($row = $result->fetch_assoc()) {
        $productId = $row["productID"];
        if ($dbType == 'production'){
            echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'><a href='product.php?id={$productId}'>{$productId}</a></td></tr>";
        } else {
            echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'><a href='product.php?id={$productId}&data=sample'>{$productId}</a></td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "No results found";
}
?>
<br/>
</section>

<section id="bestcustomer">
<h2>Most Popular Customer</h2>
<?php
// SQL: Select member count per rank
$sql = "SELECT customerID, COUNT(orderID) as order_count FROM orders Group By customerID Order by order_count DESC LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Customer " . $row['customerID'] . " has the most orders: " . $row['order_count'];
} else {
    echo "No results found";
}
?>
</section>

<section id="charts">
<?php
// Fetch membership ranks and count them
$sql = "SELECT memberRank, COUNT(*) as count FROM memberships GROUP BY memberRank ORDER BY count DESC";
$result = $conn->query($sql);

$memberRanks = [];
$memberCounts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $memberRanks[] = $row['memberRank'];
        $memberCounts[] = $row['count'];
    }
}

// Fetch return reasons and count them
$sql = "SELECT returnReason, COUNT(*) as count FROM returnedOrders GROUP BY returnReason ORDER BY count DESC";
$result = $conn->query($sql);

$returnReasons = [];
$returnCounts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $returnReasons[] = $row['returnReason'];
        $returnCounts[] = $row['count'];
    }
}

?>
<style>
    .chart-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .chart-box {
        flex: 1;
        min-width: 300px;
        max-width: 45%;
        margin: 10px;
    }
    canvas {
        max-width: 100%;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function createChart(chartId, chartType, labels, data, backgroundColor, borderColor) {
        const ctx = document.getElementById(chartId).getContext('2d');
        return new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Count',
                    data: data,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const memberRanks = <?php echo json_encode($memberRanks); ?>;
        const memberCounts = <?php echo json_encode($memberCounts); ?>;
        const returnReasons = <?php echo json_encode($returnReasons); ?>;
        const returnCounts = <?php echo json_encode($returnCounts); ?>;

        // Create charts for Member Ranks
        createChart('memberRanksChartBar', 'bar', memberRanks, memberCounts, '#0052CC', '#003399');
        createChart('memberRanksChartPie', 'pie', memberRanks, memberCounts, ['#0052CC', '#0077FF', '#33A1FF', '#66BBFF', '#99DDFF'], '#003399');

        // Create charts for Return Analysis
        createChart('returnReasonsChartBar', 'bar', returnReasons, returnCounts, '#E50010', '#B3000C');
        createChart('returnReasonsChartPie', 'pie', returnReasons, returnCounts, ['#E50010', '#FF3333', '#FF6666', '#FF9999', '#FFCCCC'], '#B3000C');
    });
</script>

<h2>Member Ranks</h2>
<div class="chart-container">
    <div class="chart-box">
        <canvas id="memberRanksChartBar"></canvas>
    </div>
    <div class="chart-box">
        <canvas id="memberRanksChartPie"></canvas>
    </div>
</div>

<h2>Return Analysis</h2>
<div class="chart-container">
    <div class="chart-box">
        <canvas id="returnReasonsChartBar"></canvas>
    </div>
    <div class="chart-box">
        <canvas id="returnReasonsChartPie"></canvas>
    </div>
</div>

</section>


<?php
$conn->close();
?>
<?php include 'includes/footer.php'; ?>