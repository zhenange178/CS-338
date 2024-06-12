<?php include 'includes/header.php'; ?>

<div>
    <h1>Initialization</h1>
    <p>Initialization consists of three parts: product data fetching, mock data generating, and database creation. Product data fetching could be updated at any time to retrieve the newest product data from H&M's website. Mock data generating and database creation should only be done once, as a first-time user. See below for details and actions.</p>
</div>
<div class="divider"></div>
<div>
    <h3>Initialize Everything</h3>
    <b>Necessary for first-time users.</b> See below initialize everything, as mentioned above. <br />
    Mock data will be randomly generated and stored in your local "mock_data" JSON file. One will be created if it doesn't already exist. Please do not modify this file. Mock data include mock users, reviews, promo codes, and other non-product data. This, along with the real product data (see below), will be used to generate the database.<br/>
    <br/><i>This will reset all data and database tables.</i>  
    <br/><br/>
    <a href="init/reset.php" class="initbutton buttonRed">Initialize everything</a>
    <br/><br/>
</div>
<div class="divider"></div>
<div>
    <h3>Update Product Data Only</h3>
    Raw data is retrieved directly from the <a href="https://www2.hm.com/en_ca/index.html" target="_blank" rel="noopener noreferrer">H&M Canada site</a> using their public API.<br />
    A simlar fraction of the newest items in each of the selected sections are used.<br />
    The raw data is stored in your local "hm_product_list" JSON file. One will be created if it doesn't already exist. Please do not modify this file.<br />
    New raw data will be added to your existing database, without replacing any current table entries.
    <br/><br/>
    <?php
    echo "Local data last updated: ";
    $filePath = 'init/data/hm_product_list.json';

    if (file_exists($filePath)) {
        $jsonData = json_decode(file_get_contents($filePath), true);
        echo isset($jsonData['requestDateTime']) ? $jsonData['requestDateTime'] : '<i>date not available</i>';
    } else {
        echo '<i>file does not exist - product data not yet initialized</i>';
    }
    ?>
    <br/><br/>
    <a href="init/update_product.php" class="initbutton buttonBlue">Update Product Data</a> <!--update link-->
    <br/><br/>
</div>
<div class="divider"></div>
<div>
    <h3>Development: Initialize Sample</h3>
    Same as the first full reset option above, but using smaller and static data files for development purposes.<br/>
    Data are stored in <code>init/data/SAMPLE_hm_product_list.json</code> and <code>init/data/SAMPLE_mock_data_list.json</code>. Make sure both files exist and contain data.<br/>
    The <code>sampledatabase</code> database will be initialized and used to store this sample data, in the exact same format.
    <br/><br/>
    <?php
    echo "SAMPLE product list JSON: ";
    $productFilePath = 'init/data/SAMPLE_hm_product_list.json';

    if (file_exists($productFilePath)) {
        echo '<a href="init/data/SAMPLE_hm_product_list.json" class="button" target="_blank" rel="noopener noreferrer">view</a>';
    } else {
        echo '<i>file does not exist</i>';
    }

    echo "<br/>SAMPLE mock data JSON: ";
    $productFilePath = 'init/data/SAMPLE_mock_data.json';

    if (file_exists($productFilePath)) {
        echo '<a href="init/data/SAMPLE_mock_data.json" class="button" target="_blank" rel="noopener noreferrer">view</a>';
    } else {
        echo '<i>file does not exist</i>';
    }
    ?>
    <br/><br/>
    <a href="init/sample.php" class="initbutton buttonOrange">Initialize Sample Database</a>
    <br/><br/>
</div>

<?php include 'includes/footer.php'; ?>