ALTER TABLE Gebruikers ADD COLUMN Type VARCHAR(20) NOT NULL DEFAULT 'klant' AFTER Tegoed;
UPDATE Gebruikers SET Type = Rol WHERE Rol IN ('beheerder', 'medewerker');
