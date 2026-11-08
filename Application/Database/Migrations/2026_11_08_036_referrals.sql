SET @fc_referral_column = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Gebruikers' AND COLUMN_NAME = 'ReferralCode') = 0,
    'ALTER TABLE Gebruikers ADD COLUMN ReferralCode VARCHAR(20) NULL AFTER Tegoed',
    'SELECT 1'
);
PREPARE fc_referral_column FROM @fc_referral_column;
EXECUTE fc_referral_column;
DEALLOCATE PREPARE fc_referral_column;

UPDATE Gebruikers
SET ReferralCode = CONCAT('REF', UPPER(SUBSTRING(REPLACE(UUID(), '-', ''), 1, 8)))
WHERE ReferralCode IS NULL OR ReferralCode = '';

SET @fc_referral_index = IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Gebruikers' AND INDEX_NAME = 'UkReferralCode') = 0,
    'ALTER TABLE Gebruikers ADD UNIQUE KEY UkReferralCode (ReferralCode)',
    'SELECT 1'
);
PREPARE fc_referral_index FROM @fc_referral_index;
EXECUTE fc_referral_index;
DEALLOCATE PREPARE fc_referral_index;

CREATE TABLE IF NOT EXISTS Referrals (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    AanbrengerId INT UNSIGNED NOT NULL,
    AangebrachtId INT UNSIGNED NOT NULL,
    Code VARCHAR(20) NOT NULL,
    Drempel INT NOT NULL DEFAULT 50000,
    Besteed INT NOT NULL DEFAULT 0,
    GekwalificeerdOp DATETIME NULL,
    AangemaaktOp DATETIME NOT NULL,
    UNIQUE KEY UkReferralLid (AangebrachtId),
    KEY IxReferralAanbrenger (AanbrengerId),
    CONSTRAINT FkReferralAanbrenger FOREIGN KEY (AanbrengerId) REFERENCES Gebruikers (Id) ON DELETE CASCADE,
    CONSTRAINT FkReferralAangebracht FOREIGN KEY (AangebrachtId) REFERENCES Gebruikers (Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ReferralBeloningen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ReferralId INT UNSIGNED NOT NULL,
    DocumentId INT UNSIGNED NOT NULL,
    BasisBedrag INT NOT NULL DEFAULT 0,
    Bedrag INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    UNIQUE KEY UkReferralDocument (ReferralId, DocumentId),
    KEY IxReferralBeloningDocument (DocumentId),
    CONSTRAINT FkReferralBeloningReferral FOREIGN KEY (ReferralId) REFERENCES Referrals (Id) ON DELETE CASCADE,
    CONSTRAINT FkReferralBeloningDocument FOREIGN KEY (DocumentId) REFERENCES Documenten (Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
