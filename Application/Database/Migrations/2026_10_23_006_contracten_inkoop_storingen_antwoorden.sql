-- Contracten, inkoopfacturen, storingen en snelle antwoorden voor tickets.
CREATE TABLE IF NOT EXISTS Contracten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    KlantId INT UNSIGNED NULL,
    Naam VARCHAR(150) NOT NULL,
    Soort VARCHAR(60) NULL,
    Status ENUM('concept','actief','opgezegd','beeindigd') NOT NULL DEFAULT 'actief',
    StartDatum DATE NOT NULL,
    EindDatum DATE NULL,
    Bedrag INT NOT NULL DEFAULT 0,
    Periode ENUM('maand','kwartaal','jaar','eenmalig') NOT NULL DEFAULT 'maand',
    Opzegtermijn VARCHAR(60) NULL,
    Notities TEXT NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxKlant (KlantId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Inkoopfacturen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Leverancier VARCHAR(150) NOT NULL,
    Factuurnummer VARCHAR(60) NULL,
    Datum DATE NOT NULL,
    VervalDatum DATE NULL,
    Bedrag INT NOT NULL DEFAULT 0,
    BtwBedrag INT NOT NULL DEFAULT 0,
    Status ENUM('open','betaald') NOT NULL DEFAULT 'open',
    Omschrijving TEXT NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxDatum (Datum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Storingen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Titel VARCHAR(150) NOT NULL,
    Status ENUM('onderzoek','opgelost','onderhoud') NOT NULL DEFAULT 'onderzoek',
    Ernst ENUM('laag','middel','hoog') NOT NULL DEFAULT 'middel',
    StartDatum DATE NOT NULL,
    EindDatum DATE NULL,
    Omschrijving TEXT NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS SnelleAntwoorden (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Titel VARCHAR(120) NOT NULL,
    Tekst TEXT NOT NULL,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO SnelleAntwoorden (Titel, Tekst, Volgorde, AangemaaktOp)
SELECT * FROM (SELECT 'Bedankt, we kijken ernaar' AS Titel, 'Bedankt voor je bericht. We gaan er direct mee aan de slag en laten je zo snel mogelijk weten wat de status is.' AS Tekst, 1 AS Volgorde, NOW() AS AangemaaktOp
UNION ALL SELECT 'Opgelost', 'Het probleem is opgelost. Kun je bevestigen dat alles weer naar behoren werkt? Dan sluiten we het ticket.', 2, NOW()
UNION ALL SELECT 'Meer informatie nodig', 'Om je goed te kunnen helpen hebben we wat meer informatie nodig. Kun je een screenshot sturen en aangeven wanneer het probleem begon?', 3, NOW()) d
WHERE NOT EXISTS (SELECT 1 FROM SnelleAntwoorden);
