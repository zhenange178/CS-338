<?php
ob_implicit_flush(1);
while (ob_get_level()) {
    ob_end_flush();
}

set_time_limit(120); 

/**
 * getApiUrl
 * Return the appropriate URL for the API request given page number and categories
 * 
 * @param string $pageNum Page number
 * @param string $pageID Item category as shown on HM website (e.g. "ladies")
 * @param string $categoryID Item category as defined in the product JSON (e.g. "ladies_all")
 * @return string the URL
 */
function getApiUrl($pageNum, $pageID, $categoryID, $isSale = false){
    if (!$isSale){
        return "https://api.hm.com/search-services/v1/en_CA/listing/resultpage?page={$pageNum}&sort=NEWEST_FIRST&pageId=/{$pageID}/shop-by-product/view-all&page-size=36&categoryId={$categoryID}&filters=sale:false||oldSale:false&touchPoint=DESKTOP&skipStockCheck=true";
    } else {
        return "https://api.hm.com/search-services/v1/en_CA/listing/resultpage?page={$pageNum}&sort=RELEVANCE&pageId=/sale/{$pageID}/view-all&page-size=36&categoryId={$categoryID}&filters=sale:true&touchPoint=DESKTOP&skipStockCheck=true";
        // sale/shopbyproductladies/view-all ladies_all
    }
}

/**
 * apiByCategory
 * Loops through pages of HM API, return list of "productList" items
 *
 * @param string $pageID Item category as shown on HM website (e.g. "ladies")
 * @param string $categoryID Item category as defined in the product JSON (e.g. "ladies_all")
 * @param double $pageLimitRatio The ratio of returned pages to the total available pages in this category, rounded down to the nearest integer. Note that the last page will always be added. Default = 0.1 (i.e. if there are 201 total pages, only add the first 20 pages plus the last full page). This is done to simulate a similar distribution of products across categories while maintaining a reasonable number of returned items.
 * @param boolean $isSale defaults false: to change API string
 * 
 * @return array all products
 */ 
function apiByCategory($pageID, $categoryID, $pageLimitRatio = 0.1, $isSale = false){
    $allProducts = [];
    $totalPages = 0; 

    // Fetch the first page
    $firstPageUrl = getApiUrl(1, $pageID, $categoryID, $isSale);
    $firstPageResponse = file_get_contents($firstPageUrl);

    if ($firstPageResponse === FALSE) {
        die("Error fetching data from API for the first page");
    }

    $firstPageData = json_decode($firstPageResponse, true);
    if (isset($firstPageData['pagination']['totalPages'])) {
        $totalPages = $firstPageData['pagination']['totalPages'];
    }

    // Determine how many pages to fetch (minimum 1)
    $maxPages = max(floor($totalPages * $pageLimitRatio), 1);
    $totalFetchPages = $maxPages + 1;
    if ($isSale){
        echo "Retrieving $totalFetchPages out of $totalPages total pages available in category <i>sale/$categoryID</i>...\n<br/>";
    } else {
        echo "Retrieving $totalFetchPages out of $totalPages total pages available in category <i>$pageID</i>...\n<br/>";
    }

    // Loop through the first $maxPages pages
    for ($page = 1; $page <= $maxPages; $page++) {
        $url = getApiUrl($page, $pageID, $categoryID, $isSale);
        $response = file_get_contents($url);

        if ($response === FALSE) {
            echo "Error fetching data from API for page $page\n";
            continue;
        }

        $data = json_decode($response, true);
        if (isset($data['plpList']['productList']) && !empty($data['plpList']['productList'])) {
            $productList = $data['plpList']['productList'];
            //$firstFiveProducts = array_slice($productList, 0, 5);
            $allProducts = array_merge($allProducts, $productList);
        } else {
            echo "No products found or 'productList' key does not exist on page $page\n";
        }
    }

    // final page
    $url = getApiUrl($totalPages - 1, $pageID, $categoryID, $isSale);
    $response = file_get_contents($url);

    if ($response === FALSE) {
        echo "Error fetching data from API for page $page\n";
    }

    $data = json_decode($response, true);
    if (isset($data['plpList']['productList']) && !empty($data['plpList']['productList'])) {
        $productList = $data['plpList']['productList'];
        $allProducts = array_merge($allProducts, $productList);
    } else {
        echo "No products found or 'productList' key does not exist on page $page\n";
    }

    flush();
    return $allProducts;
}

// Call method for different categories and merge with full array
$startTime = microtime(true);
echo '<b>Fetching newest product data from HM.com...</b><br />';
flush();
$allCategoriesProducts = [];
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('ladies', 'ladies_all', 0));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('men', 'men_all', 0));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('baby', 'kids_newbornbaby_viewall', 0));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('kids', 'kids_viewall', 0));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('home', 'home_all', 0));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('shopbyproductladies', 'ladies_all', 0, true));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('shopbyproductmen', 'men_all', 0, true));

// Build and save JSON file
$timeZone = 'America/New_York';
date_default_timezone_set($timeZone);
$dateTimeUpdated = date('Y-m-d H:i:s');

$finalData = [
    'requestDateTime' => $dateTimeUpdated,
    'timeZone'=> $timeZone,
    'productCount' => count($allCategoriesProducts),
    'products' => $allCategoriesProducts
];

echo "Writing data...<br />";

$jsonProductList = json_encode($finalData, JSON_PRETTY_PRINT);

$fileName = '../SAMPLE_hm_product_list.json';

if (file_put_contents($fileName, $jsonProductList)) {
    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;
    
    echo count($allCategoriesProducts) . " total products successfully retrieved and written to $fileName in " . number_format($executionTime, 2) . " seconds.<br /><br />";
} else {
    echo "Failed to write data to $fileName<br /><br />";
}
//echo "</div>";

flush(); // Final flush
?>