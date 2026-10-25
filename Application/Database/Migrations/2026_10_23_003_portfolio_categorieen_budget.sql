-- Portfolio: beheerbare categorieën en een budgetindicatie.
CREATE TABLE IF NOT EXISTS PortfolioCategorieen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(60) NOT NULL,
    Volgorde INT NOT NULL DEFAULT 0,
    AangemaaktOp DATETIME NOT NULL,
    BijgewerktOp DATETIME NULL,
    UNIQUE KEY UkNaam (Naam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO PortfolioCategorieen (Naam, Volgorde, AangemaaktOp) VALUES ('Development', 1, NOW()), ('Marketing', 2, NOW()), ('ICT', 3, NOW());

SET @fc_budget = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'PortfolioItems' AND COLUMN_NAME = 'Budget') = 0, 'ALTER TABLE PortfolioItems ADD COLUMN Budget VARCHAR(60) NULL AFTER Url', 'SELECT 1');
PREPARE fc_stmt FROM @fc_budget;
EXECUTE fc_stmt;
DEALLOCATE PREPARE fc_stmt;
