-- Koppelingen: Realtime Register, Mollie, Cryptomus en Qevanto hostingpaneel.

SET @fc_sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Diensten' AND COLUMN_NAME = 'Bron') = 0,
    'ALTER TABLE Diensten ADD COLUMN Bron VARCHAR(30) NULL, ADD COLUMN ExternId VARCHAR(150) NULL, ADD KEY IxExtern (Bron, ExternId)',
    'SELECT 1'
);
PREPARE fc_stmt FROM @fc_sql;
EXECUTE fc_stmt;
DEALLOCATE PREPARE fc_stmt;

SET @fc_sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Producten' AND COLUMN_NAME = 'Bron') = 0,
    'ALTER TABLE Producten ADD COLUMN Inkoopprijs INT NOT NULL DEFAULT 0 AFTER Prijs, ADD COLUMN Bron VARCHAR(30) NULL, ADD COLUMN ExternId VARCHAR(150) NULL, ADD KEY IxExtern (Bron, ExternId)',
    'SELECT 1'
);
PREPARE fc_stmt FROM @fc_sql;
EXECUTE fc_stmt;
DEALLOCATE PREPARE fc_stmt;

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
