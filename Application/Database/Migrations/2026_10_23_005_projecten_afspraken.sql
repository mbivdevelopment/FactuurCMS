-- Projecten en afspraken per klant.
CREATE TABLE IF NOT EXISTS Projecten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    KlantId INT UNSIGNED NULL,
    Naam VARCHAR(150) NOT NULL,
    Status ENUM('offerte','gepland','actief','afgerond','geannuleerd') NOT NULL DEFAULT 'gepland',
    StartDatum DATE NULL,
    EindDatum DATE NULL,
    Budget INT NOT NULL DEFAULT 0,
    Omschrijving TEXT NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxKlant (KlantId),
    KEY IxStatus (Status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Afspraken (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    KlantId INT UNSIGNED NULL,
    Onderwerp VARCHAR(150) NOT NULL,
    Datum DATE NOT NULL,
    Tijd VARCHAR(5) NULL,
    Locatie VARCHAR(150) NULL,
    Status ENUM('gepland','afgerond','geannuleerd') NOT NULL DEFAULT 'gepland',
    Notities TEXT NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxKlant (KlantId),
    KEY IxDatum (Datum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
