-- Projecten op uurbasis of vast budget, met taken per project.
SET @fc_sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Projecten' AND COLUMN_NAME = 'Facturatie') = 0, 'ALTER TABLE Projecten ADD COLUMN Facturatie ENUM(''budget'',''uurloon'') NOT NULL DEFAULT ''budget'' AFTER Status', 'SELECT 1');
PREPARE fc_stmt FROM @fc_sql;
EXECUTE fc_stmt;
DEALLOCATE PREPARE fc_stmt;
SET @fc_sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Projecten' AND COLUMN_NAME = 'Uurtarief') = 0, 'ALTER TABLE Projecten ADD COLUMN Uurtarief INT NOT NULL DEFAULT 0 AFTER Budget', 'SELECT 1');
PREPARE fc_stmt FROM @fc_sql;
EXECUTE fc_stmt;
DEALLOCATE PREPARE fc_stmt;

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
