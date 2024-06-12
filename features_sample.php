<?php include 'includes/header.php'; ?>

<h1>Features — Sample Database</h1>
<div>
    <h3>View Tables</h3>
    View all tables <a href="viewDB_sample.php">here</a>.<br />
</div>
<div>
    <h3>Edit User Data</h3>
    Edit the user's own data, name etc.<br />
    <code>UPDATE customers SET column WHERE someCondition;</code>
</div>
<div>
    <h3>Products Simple Search</h3>
    Search for products using name, id, or availablility.<br/>
    <code>SELECT id FROM products WHERE id = '1234' OR name LIKE '%something%';</code>
</div>
<div>
    <h3>Products Advanced Search</h3>
    Search for products using category/price/colors.<br/>
    Why "different" feature? Above data is stored in seperate tables, need different SQL statements to join tables.<br/>
    <code>SELECT price.product_id FROM price INNER JOIN color ON price.product_id = color.product_id WHERE price.price_condition = somePriceCondition AND color.color_condition = someColorCondition;</code>
</div>
<div>
    <h3>Create Review</h3>
    Leave review, edit review, delete review<br />
    <code>INSERT INTO; UPDATE; DELETE;</code>
</div>
<div>
    <h3>Create Order</h3>
    Create a new order, add products, promo code?
</div>
<div>
    <h3>Return Order</h3>
    (How would we want to implement returns?)<br/>
    (Inventory section? - all items bought minus all items returned?)
</div>

<?php include 'includes/footer.php'; ?>