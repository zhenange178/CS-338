-- Feature 2 - sample
SELECT promoCodeUsed, COUNT(*) as orderCount 
FROM orders 
WHERE promoCodeUsed IS NOT NULL 
GROUP BY promoCodeUsed 
ORDER BY orderCount 
DESC LIMIT 10;