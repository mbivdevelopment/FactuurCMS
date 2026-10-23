-- FactuurCMS databaseschema (MySQL/MariaDB). Importeer dit bestand en daarna seed.sql via phpMyAdmin.
-- Tabel- en kolomnamen in PascalCase; bedragen in centen excl. btw tenzij anders vermeld.

CREATE TABLE IF NOT EXISTS Gebruikers (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Nummer VARCHAR(12) NOT NULL,
    Rol ENUM('beheerder','medewerker','klant') NOT NULL DEFAULT 'klant',
    Email VARCHAR(190) NOT NULL,
    Wachtwoord VARCHAR(255) NOT NULL,
    Voornaam VARCHAR(80) NOT NULL DEFAULT '',
    Achternaam VARCHAR(80) NOT NULL DEFAULT '',
    Bedrijfsnaam VARCHAR(150) NULL,
    Telefoon VARCHAR(40) NULL,
    Adres VARCHAR(150) NULL,
    Postcode VARCHAR(12) NULL,
    Plaats VARCHAR(80) NULL,
    Land CHAR(2) NOT NULL DEFAULT 'NL',
    KvkNummer VARCHAR(20) NULL,
    BtwNummer VARCHAR(30) NULL,
    Notities TEXT NULL,
    Actief TINYINT(1) NOT NULL DEFAULT 1,
    ResetToken CHAR(64) NULL,
    ResetVerloopt DATETIME NULL,
    LaatsteLogin DATETIME NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkEmail (Email),
    UNIQUE KEY UkNummer (Nummer),
    KEY IxRol (Rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Producten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Nummer VARCHAR(12) NOT NULL,
    Groep VARCHAR(40) NOT NULL DEFAULT 'Hosting',
    Naam VARCHAR(120) NOT NULL,
    Slug VARCHAR(140) NOT NULL,
    Omschrijving TEXT NULL,
    Kenmerken JSON NULL,
    Prijs INT NOT NULL DEFAULT 0,
    Periode ENUM('eenmalig','maand','kwartaal','jaar') NOT NULL DEFAULT 'maand',
    Instelkosten INT NOT NULL DEFAULT 0,
    BtwTarief DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    Uitgelicht TINYINT(1) NOT NULL DEFAULT 0,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkSlug (Slug),
    UNIQUE KEY UkNummer (Nummer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Diensten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Nummer VARCHAR(12) NOT NULL,
    KlantId INT UNSIGNED NOT NULL,
    ProductId INT UNSIGNED NULL,
    Naam VARCHAR(150) NOT NULL,
    Omschrijving VARCHAR(255) NULL,
    Prijs INT NOT NULL DEFAULT 0,
    BtwTarief DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    Periode ENUM('eenmalig','maand','kwartaal','jaar') NOT NULL DEFAULT 'maand',
    Status ENUM('in_afwachting','actief','opgeschort','opgezegd') NOT NULL DEFAULT 'in_afwachting',
    StartDatum DATE NULL,
    VolgendeFactuur DATE NULL,
    EindDatum DATE NULL,
    Gegevens JSON NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkNummer (Nummer),
    KEY IxKlant (KlantId),
    KEY IxStatus (Status, VolgendeFactuur),
    CONSTRAINT FkDienstKlant FOREIGN KEY (KlantId) REFERENCES Gebruikers (Id) ON DELETE CASCADE,
    CONSTRAINT FkDienstProduct FOREIGN KEY (ProductId) REFERENCES Producten (Id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Facturen, creditfacturen en offertes; Totaal/Btw/Betaald in centen incl. btw.
CREATE TABLE IF NOT EXISTS Documenten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Soort ENUM('factuur','offerte') NOT NULL DEFAULT 'factuur',
    Nummer VARCHAR(20) NOT NULL,
    KlantId INT UNSIGNED NOT NULL,
    Status VARCHAR(20) NOT NULL DEFAULT 'concept',
    Datum DATE NOT NULL,
    VervalDatum DATE NULL,
    Onderwerp VARCHAR(190) NULL,
    Opmerking TEXT NULL,
    Subtotaal INT NOT NULL DEFAULT 0,
    Btw INT NOT NULL DEFAULT 0,
    Totaal INT NOT NULL DEFAULT 0,
    Betaald INT NOT NULL DEFAULT 0,
    Betalingen JSON NULL,
    BronId INT UNSIGNED NULL,
    VerzondenOp DATETIME NULL,
    BetaaldOp DATETIME NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkSoortNummer (Soort, Nummer),
    KEY IxKlant (KlantId),
    KEY IxStatus (Soort, Status),
    CONSTRAINT FkDocumentKlant FOREIGN KEY (KlantId) REFERENCES Gebruikers (Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS DocumentRegels (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    DocumentId INT UNSIGNED NOT NULL,
    DienstId INT UNSIGNED NULL,
    Omschrijving VARCHAR(500) NOT NULL,
    Aantal DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    Prijs INT NOT NULL DEFAULT 0,
    BtwTarief DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    Volgorde INT NOT NULL DEFAULT 0,
    KEY IxDocument (DocumentId),
    CONSTRAINT FkRegelDocument FOREIGN KEY (DocumentId) REFERENCES Documenten (Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Tickets (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Nummer VARCHAR(12) NOT NULL,
    KlantId INT UNSIGNED NOT NULL,
    DienstId INT UNSIGNED NULL,
    Onderwerp VARCHAR(190) NOT NULL,
    Afdeling VARCHAR(40) NOT NULL DEFAULT 'Support',
    Prioriteit ENUM('laag','normaal','hoog') NOT NULL DEFAULT 'normaal',
    Status ENUM('open','beantwoord','gesloten') NOT NULL DEFAULT 'open',
    LaatsteReactie DATETIME NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkNummer (Nummer),
    KEY IxKlant (KlantId),
    KEY IxStatus (Status),
    CONSTRAINT FkTicketKlant FOREIGN KEY (KlantId) REFERENCES Gebruikers (Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS TicketBerichten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    TicketId INT UNSIGNED NOT NULL,
    GebruikerId INT UNSIGNED NULL,
    Bericht TEXT NOT NULL,
    Intern TINYINT(1) NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    KEY IxTicket (TicketId),
    CONSTRAINT FkBerichtTicket FOREIGN KEY (TicketId) REFERENCES Tickets (Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Instellingen (
    Sleutel VARCHAR(100) NOT NULL PRIMARY KEY,
    Waarde MEDIUMTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Logboek (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Type VARCHAR(40) NOT NULL,
    GebruikerId INT UNSIGNED NULL,
    Bericht VARCHAR(255) NULL,
    Ip VARCHAR(45) NULL,
    AangemaaktOp DATETIME NOT NULL,
    KEY IxType (Type, Ip, AangemaaktOp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Migraties (
    Naam VARCHAR(190) NOT NULL PRIMARY KEY,
    UitgevoerdOp DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
