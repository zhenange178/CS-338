<?php
// includes
include 'DataGenerator.php';

ob_implicit_flush(1);
while (ob_get_level()) {
    ob_end_flush();
}

set_time_limit(120);

$idCustomer = 110000;
$idMembership = 210000;
$idOrder = 310000;
$idReview = 410000;
$mockDataPath = 'data/SAMPLE_mock_data.json';
$productDataPath = 'data/SAMPLE_hm_product_list.json';
$numCustomers = 10;
$chanceMember = 0.7;
$customerOrdersMin = 0;
$customerOrdersMax = 5;
$chanceReturn = 0.3;
$customerReviewsMin = 0;
$customerReviewsMax = 3;
$numCodes = 20;

$generator = new DataGenerator();
$generator->generateData($idCustomer, $idMembership, $idOrder, $idReview, $mockDataPath, $productDataPath, $numCustomers, $chanceMember, $customerOrdersMin, $customerOrdersMax, $chanceReturn, $customerReviewsMin, $customerReviewsMax, $numCodes);

?>