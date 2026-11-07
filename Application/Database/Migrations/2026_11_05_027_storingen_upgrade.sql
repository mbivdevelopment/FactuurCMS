-- Brengt de bestaande Storingen-tabel naar de statuspagina-structuur.
UPDATE Storingen SET Status = 'onderzocht' WHERE Status = 'onderzoek';
UPDATE Storingen SET Status = 'gepland' WHERE Status = 'onderhoud';
ALTER TABLE Storingen
    MODIFY Status ENUM('onderzocht','bevestigd','monitoring','opgelost','gepland') NOT NULL DEFAULT 'onderzocht',
    MODIFY Ernst ENUM('laag','middel','hoog','onderhoud','klein','groot','uitval') NOT NULL DEFAULT 'klein',
    CHANGE StartDatum StartTijd DATETIME NOT NULL,
    CHANGE EindDatum EindTijd DATETIME NULL,
    ADD COLUMN Dienst VARCHAR(60) NULL AFTER Omschrijving,
    ADD KEY IxStatus (Status, StartTijd);
UPDATE Storingen SET Ernst = 'klein' WHERE Ernst IN ('laag','middel');
UPDATE Storingen SET Ernst = 'groot' WHERE Ernst = 'hoog';
ALTER TABLE Storingen MODIFY Ernst ENUM('onderhoud','klein','groot','uitval') NOT NULL DEFAULT 'klein';
