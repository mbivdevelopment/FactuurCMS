-- Verwijder ongebruikte legacy SMTP-producten; gebruikte producten blijven behouden
-- voor historische diensten maar worden niet meer in de catalogus getoond.
DELETE FROM Producten
WHERE Slug IN ('smtp-relay-100', 'smtp-relay-250', 'smtp-relay-1000', 'smtp-relay-2500')
  AND NOT EXISTS (SELECT 1 FROM Diensten WHERE Diensten.ProductId = Producten.Id);

UPDATE Producten
SET Zichtbaar = 0,
    BijgewerktOp = NOW()
WHERE Slug IN ('smtp-relay-100', 'smtp-relay-250', 'smtp-relay-1000', 'smtp-relay-2500');
