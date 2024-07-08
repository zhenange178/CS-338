-- Feature 4 - sample

SELECT * FROM products 
WHERE productName LIKE '%Top%' 
AND productID LIKE '%11%' 
AND stock= 'Available';