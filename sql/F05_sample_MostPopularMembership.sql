-- Feature 5 - sample

SELECT memberRank, COUNT(*) as count
FROM memberships
GROUP BY memberRank;