<?php include 'includes/header.php'; ?>

    <h2>Data</h2>
    Raw data is retrieved directly from the <a href="https://www2.hm.com/en_ca/index.html" target="_blank" rel="noopener noreferrer">H&M Canada site</a> using their public API.<br />
    A simlar fraction of the newest items in each of the selected sections are used.<br />
    The data is stored in your local "hm_product_list" JSON file. One will be created if it doesn't already exist. Everytime you update the data (see below), your current data will be overwritten by the newest fetched data.
    <br/><br/>
    Local data last updated: <?php
        $jsonData = json_decode(file_get_contents('data/hm_product_list.json'), true);
        echo isset($jsonData['requestDateTime']) ? $jsonData['requestDateTime'] : 'Date not available';
    ?>
    <br />
    Number of products: <?php
        $jsonData = json_decode(file_get_contents('data/hm_product_list.json'), true);
        echo isset($jsonData['productCount']) ? $jsonData['productCount'] : 'Product count not available';
    ?>
    <p><a href="/data/hm_product_list.json" class="button" target="_blank" rel="noopener noreferrer">View Current Data</a> (Open new tab containing full JSON data)<br/></p>

    <p><a href="get_api.php" class="button">Update Data</a> (Run the API Script to fetch newest data from H&M site)</p>

<?php include 'includes/footer.php'; ?>