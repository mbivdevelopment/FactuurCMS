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
    Dienstsoort VARCHAR(60) NULL,
    Naam VARCHAR(120) NOT NULL,
    Slug VARCHAR(140) NOT NULL,
    Omschrijving TEXT NULL,
    Kenmerken JSON NULL,
    Prijs INT NOT NULL DEFAULT 0,
    Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','4jaar') NOT NULL DEFAULT 'maand',
    Instelkosten INT NOT NULL DEFAULT 0,
    BtwTarief DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    Uitgelicht TINYINT(1) NOT NULL DEFAULT 0,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    Inkoopprijs INT NOT NULL DEFAULT 0,
    Bron VARCHAR(30) NULL,
    ExternId VARCHAR(150) NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkSlug (Slug),
    UNIQUE KEY UkNummer (Nummer),
    KEY IxExtern (Bron, ExternId)
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
    Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','4jaar') NOT NULL DEFAULT 'maand',
    Status ENUM('in_afwachting','actief','opgeschort','opgezegd') NOT NULL DEFAULT 'in_afwachting',
    StartDatum DATE NULL,
    VolgendeFactuur DATE NULL,
    EindDatum DATE NULL,
    Gegevens JSON NULL,
    Bron VARCHAR(30) NULL,
    ExternId VARCHAR(150) NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkNummer (Nummer),
    KEY IxKlant (KlantId),
    KEY IxExtern (Bron, ExternId),
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

-- Website-modules: pagina's (megamenu), blog, portfolio, logo's, kennisbank, team, tijdlijn en datacenters.
CREATE TABLE IF NOT EXISTS Paginas (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Categorie ENUM('cloud','ict','overig') NOT NULL DEFAULT 'cloud',
    Groep VARCHAR(60) NOT NULL DEFAULT '',
    Titel VARCHAR(150) NOT NULL,
    Slug VARCHAR(160) NOT NULL,
    Icoon VARCHAR(30) NOT NULL DEFAULT 'server',
    Samenvatting VARCHAR(255) NULL,
    Inhoud MEDIUMTEXT NULL,
    Afbeelding VARCHAR(190) NULL,
    ProductGroep VARCHAR(40) NULL,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkSlug (Slug),
    KEY IxMenu (Categorie, Groep, Volgorde)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS BlogBerichten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Titel VARCHAR(190) NOT NULL,
    Slug VARCHAR(200) NOT NULL,
    Categorie VARCHAR(60) NOT NULL DEFAULT 'Nieuws',
    Auteur VARCHAR(100) NULL,
    PublicatieDatum DATE NOT NULL,
    Samenvatting VARCHAR(400) NULL,
    Inhoud MEDIUMTEXT NULL,
    Afbeelding VARCHAR(190) NULL,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkSlug (Slug),
    KEY IxDatum (Zichtbaar, PublicatieDatum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS PortfolioItems (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Titel VARCHAR(150) NOT NULL,
    Slug VARCHAR(160) NOT NULL,
    Klant VARCHAR(150) NULL,
    Categorie VARCHAR(60) NOT NULL DEFAULT 'Website',
    Url VARCHAR(255) NULL,
    Budget VARCHAR(60) NULL,
    Samenvatting VARCHAR(400) NULL,
    Inhoud MEDIUMTEXT NULL,
    Afbeelding VARCHAR(190) NULL,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkSlug (Slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS PortfolioCategorieen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(60) NOT NULL,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkNaam (Naam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Logos (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Groep VARCHAR(60) NOT NULL DEFAULT 'Webservers',
    Naam VARCHAR(100) NOT NULL,
    Url VARCHAR(255) NULL,
    Afbeelding VARCHAR(190) NULL,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxGroep (Groep, Volgorde)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS KennisbankArtikelen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Categorie VARCHAR(60) NOT NULL DEFAULT 'Algemeen',
    Titel VARCHAR(190) NOT NULL,
    Slug VARCHAR(200) NOT NULL,
    Samenvatting VARCHAR(400) NULL,
    Inhoud MEDIUMTEXT NULL,
    Weergaven INT UNSIGNED NOT NULL DEFAULT 0,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkSlug (Slug),
    KEY IxCategorie (Categorie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Teamleden (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(120) NOT NULL,
    Functie VARCHAR(120) NULL,
    Bio TEXT NULL,
    Email VARCHAR(190) NULL,
    LinkedIn VARCHAR(255) NULL,
    Afbeelding VARCHAR(190) NULL,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Tijdlijn (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Jaar VARCHAR(20) NOT NULL,
    Titel VARCHAR(150) NOT NULL,
    Soort ENUM('mijlpaal','lancering','overname') NOT NULL DEFAULT 'mijlpaal',
    Verhaal TEXT NULL,
    Afbeelding VARCHAR(190) NULL,
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Datacenters (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(120) NOT NULL,
    Stad VARCHAR(100) NOT NULL,
    Land VARCHAR(100) NOT NULL,
    Latitude DECIMAL(9,5) NOT NULL DEFAULT 0,
    Longitude DECIMAL(9,5) NOT NULL DEFAULT 0,
    Omschrijving VARCHAR(400) NULL,
    Status ENUM('actief','binnenkort') NOT NULL DEFAULT 'actief',
    Zichtbaar TINYINT(1) NOT NULL DEFAULT 1,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projecten en afspraken per klant.
CREATE TABLE IF NOT EXISTS Projecten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    KlantId INT UNSIGNED NULL,
    Naam VARCHAR(150) NOT NULL,
    Status ENUM('offerte','gepland','actief','afgerond','geannuleerd') NOT NULL DEFAULT 'gepland',
    Facturatie ENUM('budget','uurloon') NOT NULL DEFAULT 'budget',
    StartDatum DATE NULL,
    EindDatum DATE NULL,
    Budget INT NOT NULL DEFAULT 0,
    Uurtarief INT NOT NULL DEFAULT 0,
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

CREATE TABLE IF NOT EXISTS Contracten (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    KlantId INT UNSIGNED NULL,
    Naam VARCHAR(150) NOT NULL,
    Soort VARCHAR(60) NULL,
    Status ENUM('concept','actief','opgezegd','beeindigd') NOT NULL DEFAULT 'actief',
    StartDatum DATE NOT NULL,
    EindDatum DATE NULL,
    Bedrag INT NOT NULL DEFAULT 0,
    Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','4jaar') NOT NULL DEFAULT 'maand',
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

CREATE TABLE IF NOT EXISTS ProjectTaken (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ProjectId INT UNSIGNED NOT NULL,
    Titel VARCHAR(150) NOT NULL,
    Omschrijving TEXT NULL,
    Status ENUM('open','bezig','klaar') NOT NULL DEFAULT 'open',
    MedewerkerId INT UNSIGNED NULL,
    Deadline DATE NULL,
    GeschatteUren DECIMAL(7,2) NOT NULL DEFAULT 0,
    GewerkteUren DECIMAL(7,2) NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxProject (ProjectId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS OnlineBetalingen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    DocumentId INT UNSIGNED NOT NULL,
    Provider VARCHAR(20) NOT NULL,
    ExternId VARCHAR(120) NULL,
    Bedrag INT NOT NULL DEFAULT 0,
    Status ENUM('open','betaald','mislukt') NOT NULL DEFAULT 'open',
    ProviderStatus VARCHAR(40) NULL,
    Url VARCHAR(500) NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxDocument (DocumentId),
    KEY IxExtern (Provider, ExternId),
    CONSTRAINT FkOnlineDocument FOREIGN KEY (DocumentId) REFERENCES Documenten (Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
