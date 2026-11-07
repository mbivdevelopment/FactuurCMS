-- Oude demo-webhostingpakketten (Start/Pro/Business/Premium uit seed.sql) opruimen zodra er
-- echte pakketten uit het Qevanto-paneel zijn: ongebruikt => verwijderen, in gebruik => verbergen.
DELETE FROM Producten
WHERE Bron IS NULL AND Dienstsoort = 'Webhosting'
  AND Slug IN ('webhosting-start', 'webhosting-pro', 'webhosting-business', 'webhosting-premium')
  AND EXISTS (SELECT 1 FROM (SELECT Id FROM Producten WHERE Bron = 'qevanto' AND Dienstsoort = 'Webhosting') q)
  AND NOT EXISTS (SELECT 1 FROM Diensten d WHERE d.ProductId = Producten.Id);

UPDATE Producten SET Zichtbaar = 0
WHERE Bron IS NULL AND Dienstsoort = 'Webhosting' AND Zichtbaar = 1
  AND Slug IN ('webhosting-start', 'webhosting-pro', 'webhosting-business', 'webhosting-premium')
  AND EXISTS (SELECT 1 FROM (SELECT Id FROM Producten WHERE Bron = 'qevanto' AND Dienstsoort = 'Webhosting') q);
