-- Netwerkstatus/storingen voor de publieke statusbalk en /storingen.
CREATE TABLE IF NOT EXISTS Storingen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Titel VARCHAR(190) NOT NULL,
    Omschrijving TEXT NULL,
    Dienst VARCHAR(60) NULL,
    Ernst ENUM('onderhoud','klein','groot','uitval') NOT NULL DEFAULT 'klein',
    Status ENUM('onderzocht','bevestigd','monitoring','opgelost','gepland') NOT NULL DEFAULT 'onderzocht',
    StartTijd DATETIME NOT NULL,
    EindTijd DATETIME NULL,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    KEY IxStatus (Status, StartTijd)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
