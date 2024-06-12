<?php include 'includes/header.php'; ?>

<div>
    <h1>Initialization</h1>
    <p>Initialization consists of three parts: product data fetching, mock data generating, and database creation. Product data fetching could be updated at any time to retrieve the newest product data from H&M's website. Mock data generating and database creation should only be done once, as a first-time user. See below for details and actions.</p>
</div>
<div class="divider"></div>
<div>
    <h2>Initialize Everything</h2>
    <b>Necessary for first-time users.</b> See below initialize everything, as mentioned above. <br />
    Mock data will be randomly generated and stored in your local "mock_data" JSON file. One will be created if it doesn't already exist. Please do not modify this file. Mock data include mock users, reviews, promo codes, and other non-product data. This, along with the real product data (see below), will be used to generate the database.<br/>
    <b>This will reset all data and database tables.</b>  
    <br/><br/>
    <a href="init/reset.php" class="initbutton buttonRed">Initialize everything</a>
    <br/><br/>
</div>
<div class="divider"></div>
<div>
    <h2>Update Product Data Only</h2>
    Raw data is retrieved directly from the <a href="https://www2.hm.com/en_ca/index.html" target="_blank" rel="noopener noreferrer">H&M Canada site</a> using their public API.<br />
    A simlar fraction of the newest items in each of the selected sections are used.<br />
    The raw data is stored in your local "hm_product_list" JSON file. One will be created if it doesn't already exist. Please do not modify this file.<br />
    New raw data will be added to your existing database, without replacing any current table entries.
    <br/><br/>
    <?php
    echo "Local data last updated: ";
    $filePath = 'init/hm_product_list.json';

    if (file_exists($filePath)) {
        $jsonData = json_decode(file_get_contents($filePath), true);
        echo isset($jsonData['requestDateTime']) ? $jsonData['requestDateTime'] : '<i>date not available</i>';
    } else {
        echo '<i>file does not exist - product data not yet initialized</i>';
    }
    ?>
    <br />
    <!-- Number of products: <?php
        $jsonData = json_decode(file_get_contents('init/hm_product_list.json'), true);
        echo isset($jsonData['productCount']) ? $jsonData['productCount'] : 'Product count not available';
    ?> -->
    <!-- <p><a href="hm_product_list.json" class="button" target="_blank" rel="noopener noreferrer">View Current Raw Data</a> (Open new tab containing full JSON data)<br/></p> -->
    <br/>
    <a href="init/update_product.php" class="initbutton buttonBlue">Update Product Data</a> <!--update link-->
    <br/><br/>
    <!-- <p><a href="get_api.php" class="button">Update Raw Data</a> (Run the API Script to fetch newest data from H&M site)</p> -->
</div>

<?php include 'includes/footer.php'; ?>