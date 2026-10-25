-- Domeinextensies uit Realtime Register: slug "domein-nl" wordt "nl" (URL /extensies/nl).
UPDATE Producten p
JOIN (SELECT Id, SUBSTRING(Slug, 8) AS NieuweSlug FROM Producten WHERE Bron = 'realtimeregister' AND Slug LIKE 'domein-%') n ON n.Id = p.Id
LEFT JOIN (SELECT Slug FROM Producten) bestaand ON bestaand.Slug = n.NieuweSlug
SET p.Slug = n.NieuweSlug
WHERE bestaand.Slug IS NULL;
