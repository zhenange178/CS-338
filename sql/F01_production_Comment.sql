-- Feature 1 - sample

-- insert review
INSERT INTO reviews (customerID, productID, rating, comment) 
VALUES (100000, 1187511001, 5, 'this is a comment');

-- update review
UPDATE reviews 
SET customerID = 100000, productID = 1187511001, rating = 4, comment = 'colors washed off again' 
WHERE reviewID = 400144;

-- select
SELECT * FROM reviews
WHERE productID = 1187511001;