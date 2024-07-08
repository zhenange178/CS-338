-- Feature 6 - production
SELECT customerID, COUNT(orderID) as order_count 
FROM orders 
Group By customerID 
Order By order_count 
DESC
LIMIT 1;