-- Crediteuren (leveranciers) en leads onder Klanten → Relaties.
CREATE TABLE IF NOT EXISTS Crediteuren (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Bedrijfsnaam VARCHAR(150) NOT NULL,
    Contactpersoon VARCHAR(120) NULL,
    Email VARCHAR(190) NULL,
    Telefoon VARCHAR(40) NULL,
    Iban VARCHAR(40) NULL,
    KvkNummer VARCHAR(20) NULL,
    BtwNummer VARCHAR(30) NULL,
    Notities TEXT NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxBedrijfsnaam (Bedrijfsnaam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Leads (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(120) NOT NULL,
    Bedrijf VARCHAR(150) NULL,
    Email VARCHAR(190) NULL,
    Telefoon VARCHAR(40) NULL,
    Bron ENUM('website','telefoon','email','doorverwijzing','beurs','overig') NOT NULL DEFAULT 'website',
    Status ENUM('nieuw','contact','offerte','gewonnen','verloren') NOT NULL DEFAULT 'nieuw',
    Waarde INT NOT NULL DEFAULT 0,
    VolgendeActie DATE NULL,
    Notities TEXT NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxStatus (Status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
