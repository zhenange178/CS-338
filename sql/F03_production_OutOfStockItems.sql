-- Feature 3 - production
-- NOTE: production data doesn't have out of stock items at this time - the output file will be blank
SELECT productID 
FROM products 
WHERE Stock = 'NotAvailable';