-- Feature 1 - sample

-- insert review
INSERT INTO reviews (customerID, productID, rating, comment) 
VALUES (100000, 679687085, 5, 'this is a comment');

-- update review
UPDATE reviews 
SET customerID = 100000, productID = 679687085, rating = 4, comment = 'colors washed off' 
WHERE reviewID = 410013;

-- select
SELECT * FROM reviews
WHERE productID = 679687085;