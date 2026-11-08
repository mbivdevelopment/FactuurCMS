-- Vier verkoopproducten voor SMTP Relay. Bedragen zijn eurocenten per maand.
UPDATE Producten
SET Naam = 'SMTP Relay 100',
    Slug = 'smtp-relay-100',
    Omschrijving = 'Betrouwbare e-mailverzending voor websites en applicaties.',
    Kenmerken = '["100 e-mails per maand","SMTP-toegang","SPF, DKIM en DMARC","Verzendlimiet en afzenderdomeinen"]',
    Prijs = 495,
    Volgorde = 1,
    BijgewerktOp = NOW()
WHERE Slug = 'smtp-relay-start';

SET @fc_n = (SELECT COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) FROM Producten WHERE Nummer REGEXP '^A[0-9]+$');

INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD((SELECT COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) + 1 FROM Producten WHERE Nummer REGEXP '^A[0-9]+$'), 5, '0')),
  'E-mail', 'SMTP Relay', 'SMTP Relay 250', 'smtp-relay-250',
  'Betrouwbare e-mailverzending voor websites en applicaties.',
  '["250 e-mails per maand","SMTP-toegang","SPF, DKIM en DMARC","Verzendlimiet en afzenderdomeinen"]',
  795, 'maand', 0, 21.00, 0, 1, 2, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = 'smtp-relay-250');

INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD((SELECT COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) + 1 FROM Producten WHERE Nummer REGEXP '^A[0-9]+$'), 5, '0')),
  'E-mail', 'SMTP Relay', 'SMTP Relay 1000', 'smtp-relay-1000',
  'Betrouwbare e-mailverzending voor websites en applicaties.',
  '["1.000 e-mails per maand","SMTP-toegang","SPF, DKIM en DMARC","Verzendlimiet en afzenderdomeinen"]',
  1495, 'maand', 0, 21.00, 0, 1, 3, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = 'smtp-relay-1000');

INSERT INTO Producten (Nummer, Groep, Dienstsoort, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD((SELECT COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) + 1 FROM Producten WHERE Nummer REGEXP '^A[0-9]+$'), 5, '0')),
  'E-mail', 'SMTP Relay', 'SMTP Relay 2500', 'smtp-relay-2500',
  'Betrouwbare e-mailverzending voor websites en applicaties.',
  '["2.500 e-mails per maand","SMTP-toegang","SPF, DKIM en DMARC","Verzendlimiet en afzenderdomeinen"]',
  2495, 'maand', 0, 21.00, 0, 1, 4, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Producten WHERE Slug = 'smtp-relay-2500');
