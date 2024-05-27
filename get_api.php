<?php

/**
 * getApiUrl
 * Return the appropriate URL for the API request given page number and categories
 * 
 * @param string $pageNum Page number
 * @param string $pageID Item category as shown on HM website (e.g. "ladies")
 * @param string $categoryID Item category as defined in the product JSON (e.g. "ladies_all")
 * @return string the URL
 */
function getApiUrl($pageNum, $pageID, $categoryID){
    return "https://api.hm.com/search-services/v1/en_CA/listing/resultpage?page={$pageNum}&sort=NEWEST_FIRST&pageId=/{$pageID}/shop-by-product/view-all&page-size=36&categoryId={$categoryID}&filters=sale:false||oldSale:false&touchPoint=DESKTOP&skipStockCheck=true";
}

/**
 * apiByCategory
 * Loops through pages of HM API, return list of "productList" items
 *
 * @param string $pageID Item category as shown on HM website (e.g. "ladies")
 * @param string $categoryID Item category as defined in the product JSON (e.g. "ladies_all")
 * @param double $pageLimitRatio The ratio of returned pages to the total available pages in this category, rounded down to the nearest integer. Default = 0.1 (i.e. if there are 201 total pages, only add the first 20 pages). This is done to simulate a similar distribution of products across categories while maintaining a reasonable number of returned items.
 * 
 * @return array all products
 */ 
function apiByCategory($pageID, $categoryID, $pageLimitRatio = 0.1){
    $allProducts = [];
    $totalPages = 0; 

    // Fetch the first page
    $firstPageUrl = getApiUrl(1, $pageID, $categoryID);
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
    echo "Retrieving newest $maxPages out of $totalPages total pages available in category $pageID...\n<br/>";

    // Loop through the first $maxPages pages
    for ($page = 1; $page <= $maxPages; $page++) {
        $url = getApiUrl($page, $pageID, $categoryID);
        $response = file_get_contents($url);

        if ($response === FALSE) {
            echo "Error fetching data from API for page $page\n";
            continue;
        }

        $data = json_decode($response, true);
        if (isset($data['plpList']['productList']) && !empty($data['plpList']['productList'])) {
            $productList = $data['plpList']['productList'];
            $allProducts = array_merge($allProducts, $productList);
        } else {
            echo "No products found or 'productList' key does not exist on page $page\n";
        }
    }

    return $allProducts;
}

// Call method for different categories and merge with full array
echo "Fetching product data from HM.com...\n<br/>";
$allCategoriesProducts = [];
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('ladies', 'ladies_all', 0.1));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('men', 'men_all', 0.1));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('baby', 'kids_newbornbaby_viewall', 0.1));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('kids', 'kids_viewall', 0.1));
$allCategoriesProducts = array_merge($allCategoriesProducts, apiByCategory('home', 'home_all', 0.1));

// Encode all products into a new JSON string
$jsonProductList = json_encode($allCategoriesProducts, JSON_PRETTY_PRINT);

// Save the JSON data to a file
$fileName = 'hm_product_list.json';

if (file_put_contents($fileName, $jsonProductList)) {
    echo "Data successfully written to $fileName";
} else {
    echo "Failed to write data to $fileName";
}

// TODO: save the time called as another field: display "last fetched"
?>