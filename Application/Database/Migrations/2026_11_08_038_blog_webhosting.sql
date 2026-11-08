-- Markeer bestaande hostingartikelen voor de webhostingpagina.
UPDATE BlogBerichten
SET Categorie = 'Webhosting'
WHERE Slug IN (
    'kritieke-php-update',
    'wordpress-updaten',
    'ssl-certificaten-uitleg',
    'dagelijkse-backups',
    'website-traag-oorzaken'
);
