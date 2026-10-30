-- Elke dienstsoort precies 4 pakketten: vierde CloudVPS-pakket en Webhosting Premium
-- erbij, losse producten (VPS Basic, CloudVPS 1 slice, Website laten maken) verbergen.
SET @fc_n = (SELECT COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) FROM Producten WHERE Nummer REGEXP '^A[0-9]+$');

INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, Bron, AangemaaktOp)
SELECT CONCAT('A', LPAD(@fc_n := @fc_n + 1, 5, '0')), 'VPS', 'Cloud VPS', 'CloudVPS Enterprise', 'cloudvps-enterprise', 'Maximale performance voor kritieke systemen.', '["8 vCPU slices","Linux of Windows","KVM-virtualisatie","Volledige root-toegang","Prioriteit provisioning"]', 7995, 'maand', 0, 21.00, 0, 1, 4, 'interserver', NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = 'cloudvps-enterprise');

INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD(@fc_n := @fc_n + 1, 5, '0')), 'Hosting', 'Webhosting', 'Webhosting Premium', 'webhosting-premium', 'Voor maximale performance en meerdere websites.', '["Onbeperkt NVMe-opslag","Onbeperkt websites","Onbeperkt e-mail","Dedicated resources","Prioriteit support"]', 2495, 'maand', 0, 21.00, 0, 1, 4, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = 'webhosting-premium');

UPDATE Producten SET Zichtbaar = 0
WHERE Zichtbaar = 1 AND Dienstsoort IN ('Cloud VPS', 'CloudVPS')
  AND Slug NOT IN ('cloudvps-start', 'cloudvps-pro', 'cloudvps-business', 'cloudvps-enterprise');

UPDATE Producten SET Zichtbaar = 0 WHERE Slug = 'website-laten-maken' AND Zichtbaar = 1;
