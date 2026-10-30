-- Drie CloudVPS-pakketten gekoppeld aan InterServer (Bron = 'interserver'):
-- klant configureert platform/slices/OS in de bestelflow, na betaling provisioned automatisch.
SET @fc_n = (SELECT COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) FROM Producten WHERE Nummer REGEXP '^A[0-9]+$');
INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, Bron, AangemaaktOp)
SELECT CONCAT('A', LPAD(@fc_n := @fc_n + 1, 5, '0')), 'VPS', 'Cloud VPS', t.Naam, t.Slug, t.Omschrijving, t.Kenmerken, t.Prijs, 'maand', 0, 21.00, t.Uitgelicht, 1, t.Volgorde, 'interserver', NOW()
FROM (
SELECT 'CloudVPS Start' AS Naam, 'cloudvps-start' AS Slug, 'Instap-VPS voor kleine sites en testomgevingen.' AS Omschrijving, '["1 vCPU slice","Linux of Windows","KVM-virtualisatie","Volledige root-toegang","Kies je eigen OS en locatie"]' AS Kenmerken, 995 AS Prijs, 0 AS Uitgelicht, 1 AS Volgorde
UNION ALL SELECT 'CloudVPS Pro', 'cloudvps-pro', 'Voor webshops, API''s en middelgrote workloads.', '["2 vCPU slices","Linux of Windows","KVM-virtualisatie","Volledige root-toegang","Start/stop/reboot in je account"]', 1995, 1, 2
UNION ALL SELECT 'CloudVPS Business', 'cloudvps-business', 'Zware workloads en productie-omgevingen.', '["4 vCPU slices","Linux of Windows","KVM-virtualisatie","Volledige root-toegang","Prioriteit provisioning"]', 3995, 0, 3
UNION ALL SELECT 'CloudVPS Enterprise', 'cloudvps-enterprise', 'Maximale performance voor kritieke systemen.', '["8 vCPU slices","Linux of Windows","KVM-virtualisatie","Volledige root-toegang","Prioriteit provisioning"]', 7995, 0, 4
) t
WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = t.Slug);

-- Oude losse VPS-producten verbergen zodat Cloud VPS precies 4 pakketten telt.
UPDATE Producten SET Zichtbaar = 0
WHERE Zichtbaar = 1 AND Dienstsoort IN ('Cloud VPS', 'CloudVPS')
  AND Slug NOT IN ('cloudvps-start', 'cloudvps-pro', 'cloudvps-business', 'cloudvps-enterprise');

-- Webhosting ook op 4 pakketten: Webhosting Premium toevoegen als die ontbreekt.
INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD(@fc_n := @fc_n + 1, 5, '0')), 'Hosting', 'Webhosting', 'Webhosting Premium', 'webhosting-premium', 'Voor maximale performance en meerdere websites.', '["Onbeperkt NVMe-opslag","Onbeperkt websites","Onbeperkt e-mail","Dedicated resources","Prioriteit support"]', 2495, 'maand', 0, 21.00, 0, 1, 4, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = 'webhosting-premium');

-- Losse product "Website laten maken" verbergen: Informatieve Websites heeft al 4 pakketten.
UPDATE Producten SET Zichtbaar = 0 WHERE Slug = 'website-laten-maken' AND Zichtbaar = 1;
