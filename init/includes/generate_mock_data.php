<?php
ob_implicit_flush(1);
while (ob_get_level()) {
    ob_end_flush();
}

set_time_limit(120);

// Define ID start values
$idCustomer = 100000;
$idMembership = 200000;
$idOrder = 300000;
$idReview = 400000;

/**
 * randomDate
 * Return a random date, used for birthday/expiration date generation
 * 
 * @param date $start_date earliest date to choose
 * @param date $end_date latest date to choose
 * @return date a random date between the two (inclusive)
 */
function randomDate($start_date, $end_date) {
    $min = strtotime($start_date);
    $max = strtotime($end_date);
    $val = rand($min, $max);
    return date('Y-m-d', $val);
}

/**
 * randomDateTime
 * Return a random date and time, used for orders date generation
 * 
 * @param date $start_date earliest date to choose
 * @param date $end_date latest date to choose
 * @return date a random date between the two (inclusive)
 */
function randomDateTime($start_date, $end_date) {
    $min = strtotime($start_date);
    $max = strtotime($end_date);
    $val = rand($min, $max);
    return date('Y-m-d H:i:s', $val); 
}

/**
 * randomString
 * Return a random string, used for promo code generation
 * 
 * @param int $length defaults 5, length of string to generate
 * @return string random string chosen from digits 0-9, a-z, A-Z
 */
function randomString($length = 5) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * randomDiscount
 * Return a random discount type, used for promo code generation
 * 
 * @return string 'percent_off' or 'amount_off' with equal probability
 */
function randomDiscount(){
    if (rand(0,1)){
        return 'percent_off';
    }
    return 'amount_off';
}

/**
 * randomBoolean
 * 
 * @return boolean true or false with equal probability
 */
function randomBoolean() {
    return rand(0, 1) == 1;
}

$startTime = microtime(true);
echo "<b>Generating Mock Data...</b><br />";
flush();

/**
 * Generate Customers
 */

// Name and location list for random
$firstNames = ['James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda', 'William', 'Elizabeth'];
$lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
$cityCountries = [
    ['City' => 'New York', 'Country' => 'USA'],
    ['City' => 'London', 'Country' => 'UK'],
    ['City' => 'Tokyo', 'Country' => 'Japan'],
    ['City' => 'Paris', 'Country' => 'France'],
    ['City' => 'Sydney', 'Country' => 'Australia'],
    ['City' => 'Berlin', 'Country' => 'Germany'],
    ['City' => 'Moscow', 'Country' => 'Russia'],
    ['City' => 'Toronto', 'Country' => 'Canada'],
    ['City' => 'Beijing', 'Country' => 'China'],
    ['City' => 'Sao Paulo', 'Country' => 'Brazil']
];
$streetNames = [
    'Main', 'Second', 'Oak', 'Third', 'Park',
    'Fifth', 'Maple', 'Mulberry', 'Sixth', 'Pine',
    'Cedar', 'Eighth', 'Elm', 'View', 'Washington',
    'Ninth', 'Lake', 'Hill'
];

// Generate mock customers
$customers = [];
for ($i = $idCustomer+1; $i <= $idCustomer+100; $i++) {
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    $cityCountry = $cityCountries[array_rand($cityCountries)];
    $streetName = $streetNames[array_rand($streetNames)];
    $customer = [
        'ID' => $i,
        'Birth' => randomDate('1950-01-01', '2000-12-31'),
        'Phone' => '+1-' . rand(200, 999) . '-' . rand(100, 999) . '-' . rand(1000, 9999),
        'Address' => rand(1, 100) . ' ' . $streetName . ', ' . $cityCountry['City'] . ', ' . $cityCountry['Country'],
        'Email' => strtolower($firstName . '.' . $lastName . '@example.com'),
        'FName' => $firstName,
        'LName' => $lastName
    ];
    $customers[] = $customer;
}

echo 'Generated ' . count($customers) . ' customers, ';
flush();

/**
 * Generate Memberships
 */
$memberships = [];
foreach ($customers as $customer) {
    if (rand(0,2)){ // Randomly decide to add membership
        $membership = [
            'MemberID' => ++$idMembership,
            'CustomerID' => $customer['ID'],
            'Price' => rand(1,99),
            'Expiration' => randomDate('2024-06-01', '2034-01-01'),
            'Rank' => rand(1,6),
        ];
        $memberships[] = $membership;
    }
} 

echo count($memberships) . ' memberships, ';
flush();

/**
 * Generate Orders
 */

// Generate mock orders
$orders = [];
$orderId = $idOrder + 1;
foreach ($customers as $customer) {
    $numOrders = rand(0, 5); // Each customer can have between 0 and 5 orders
    for ($j = 0; $j < $numOrders; $j++) {
        $order = [
            'OrderID' => $orderId,
            'CustomerID' => $customer['ID'],
            'TrackingID' => rand(100000, 999999),
            'DateTime' => randomDateTime('2020-01-01', '2024-05-31')
        ];
        $orders[] = $order;
        $orderId++;
    }
}

echo count($orders) . ' orders, ';
flush();

/**
 * Generate Reviews
 */

// Load product data from json
$productData = json_decode(file_get_contents('data/hm_product_list.json'), true)['products'];
$productIds = array_map(function ($product) {
    return $product['id'];
}, $productData);

// Generate review data
$reviews = [];
foreach ($customers as $customer) {
    $numReviews = rand(0, 3);  // Each customer can have between 0 and 3 reviews
    for ($i = 0; $i < $numReviews; $i++) {
        $review = [
            'ReviewID' => ++$idReview,
            'CustomerID' => $customer['ID'],
            'Rating' => rand(1, 5),  // Random rating out of 5
            'Comment' => '',  // Assume blank comments for simplicity
            'ProductID' => $productIds[array_rand($productIds)]  // Random product ID from the list
        ];
        if (rand(0, 1)) {  // Randomly decide to add a comment
            $review['Comment'] = 'Sample comment for product ID ' . $review['ProductID'];
        }
        $reviews[] = $review;
    }
}

echo count($reviews) . ' reviews, ';
flush();

/**
 * Generate Promo Codes
 */

// Generate codes data
$codes = [];
for ($i = 0; $i < 300; $i++) {
    $code = [
        'PromoCode' => randomString(),
        'Source' => '',
        'TotalAvailable' => rand(10, 1000),
        'isMemberOnly' => randomBoolean(),
        'Expiration' => randomDate('2024-06-01', '2034-01-01'),
        'DiscountType' => randomDiscount(),
        'DiscountAmount' => rand(5, 50),
        'RestrictionAmount' => rand(50, 150)
    ];
    $codes[] = $code;
}

echo count($codes) . ' promo codes.<br/>';
flush();


// Prepare combined data
$data = [
    'customers' => $customers,
    'memberships' => $memberships,
    'orders' => $orders,
    'reviews' => $reviews,
    'promoCodes' => $codes
];

// Encode data to JSON
echo "Writing data...<br />";
$jsonData = json_encode($data, JSON_PRETTY_PRINT);

$fileName = 'data/mock_data.json';

if (file_put_contents($fileName, $jsonData)){
    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;

    echo "Mock data successfully generated and written to $fileName in " . number_format($executionTime, 2) . " seconds.<br/ ><br />";
} else {
    echo "Failed to write data to $fileName<br /><br />";
}
flush();
?>