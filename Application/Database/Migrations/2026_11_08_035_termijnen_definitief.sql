ALTER TABLE Producten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','halfjaar','jaar','2jaar','3jaar','4jaar','5jaar','6jaar','8jaar') NOT NULL DEFAULT 'maand';
ALTER TABLE Diensten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','halfjaar','jaar','2jaar','3jaar','4jaar','5jaar','6jaar','8jaar') NOT NULL DEFAULT 'maand';
ALTER TABLE Contracten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','halfjaar','jaar','2jaar','3jaar','4jaar','5jaar','6jaar','8jaar') NOT NULL DEFAULT 'maand';

UPDATE Producten SET Periode = '8jaar' WHERE Periode = '6jaar';
UPDATE Diensten SET Periode = '8jaar' WHERE Periode = '6jaar';
UPDATE Contracten SET Periode = '8jaar' WHERE Periode = '6jaar';

ALTER TABLE Producten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','halfjaar','jaar','2jaar','3jaar','4jaar','5jaar','8jaar') NOT NULL DEFAULT 'maand';
ALTER TABLE Diensten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','halfjaar','jaar','2jaar','3jaar','4jaar','5jaar','8jaar') NOT NULL DEFAULT 'maand';
ALTER TABLE Contracten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','halfjaar','jaar','2jaar','3jaar','4jaar','5jaar','8jaar') NOT NULL DEFAULT 'maand';
