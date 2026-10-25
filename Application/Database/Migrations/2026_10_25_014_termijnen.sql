ALTER TABLE Producten MODIFY Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','4jaar') NOT NULL DEFAULT 'maand';
ALTER TABLE Diensten MODIFY Periode ENUM('eenmalig','maand','kwartaal','jaar','2jaar','4jaar') NOT NULL DEFAULT 'maand';
