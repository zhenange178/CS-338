<?php
// includes
include 'DataGenerator.php';

ob_implicit_flush(1);
while (ob_get_level()) {
    ob_end_flush();
}

set_time_limit(120);

$idCustomer = 100000;
$idMembership = 200000;
$idOrder = 300000;
$idReview = 400000;
$mockDataPath = 'data/mock_data.json';
$productDataPath = 'data/hm_product_list.json';
$numCustomers = 100;
$chanceMember = 0.7;
$customerOrdersMin = 0;
$customerOrdersMax = 5;
$chanceReturn = 0.3;
$customerReviewsMin = 0;
$customerReviewsMax = 3;
$numCodes = 250;

$generator = new DataGenerator();
$generator->generateData($idCustomer, $idMembership, $idOrder, $idReview, $mockDataPath, $productDataPath, $numCustomers, $chanceMember, $customerOrdersMin, $customerOrdersMax, $chanceReturn, $customerReviewsMin, $customerReviewsMax, $numCodes);

?>