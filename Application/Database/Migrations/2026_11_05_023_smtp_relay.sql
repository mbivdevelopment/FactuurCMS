-- Dienstsoort SMTP Relay + starterspakket als product, zodat de dienst verkoopbaar is.
SET @fc_n = (SELECT COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) FROM Producten WHERE Nummer REGEXP '^A[0-9]+$');

INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD(@fc_n + 1, 5, '0')),
  'E-mail', 'SMTP Relay', 'SMTP Relay Start', 'smtp-relay-start',
  'Betrouwbare e-mailverzending voor websites en applicaties.',
  '["10.000 e-mails per maand","SMTP- en API-toegang","SPF, DKIM en DMARC","Realtime bezorgtracking","Bounce- en complaint-verwerking","Support via tickets"]',
  495, 'maand', 0, 21.00, 0, 1, 1, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = 'smtp-relay-start');
