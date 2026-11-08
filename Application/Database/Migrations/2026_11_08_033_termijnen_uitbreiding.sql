ALTER TABLE Producten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','3jaar','4jaar','5jaar','6jaar') NOT NULL DEFAULT 'maand';

ALTER TABLE Diensten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','3jaar','4jaar','5jaar','6jaar') NOT NULL DEFAULT 'maand';

ALTER TABLE Contracten
    MODIFY Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','3jaar','4jaar','5jaar','6jaar') NOT NULL DEFAULT 'maand';
