-- Feature 5 - production

SELECT memberRank, COUNT(*) as count
FROM memberships
GROUP BY memberRank;