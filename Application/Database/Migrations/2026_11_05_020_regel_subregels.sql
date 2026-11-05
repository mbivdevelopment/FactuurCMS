-- Subregels op documentregels: periode, verloopdatum en dienst-details als JSON-lijst.
ALTER TABLE DocumentRegels ADD COLUMN Subregels TEXT NULL AFTER Omschrijving;
