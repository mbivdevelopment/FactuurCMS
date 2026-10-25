-- Dienstsoort per product voor het Diensten-megamenu in het beheer.
SET @fc_soort = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Producten' AND COLUMN_NAME = 'Dienstsoort') = 0, 'ALTER TABLE Producten ADD COLUMN Dienstsoort VARCHAR(60) NULL AFTER Groep', 'SELECT 1');
PREPARE fc_stmt FROM @fc_soort;
EXECUTE fc_stmt;
DEALLOCATE PREPARE fc_stmt;
UPDATE Producten SET Dienstsoort = CASE Groep WHEN 'Hosting' THEN 'Webhosting' WHEN 'Domeinen' THEN 'Domeinnamen' WHEN 'VPS' THEN 'Cloud VPS' WHEN 'E-mail' THEN 'E-mail Hosting' WHEN 'Websites' THEN 'Informatieve Websites' ELSE NULL END WHERE Dienstsoort IS NULL;
