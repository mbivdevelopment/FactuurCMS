-- Kolommen voor het ICT & Development-megamenu (alleen pagina's zonder eigen kolom).
UPDATE Paginas SET Groep = 'Connectiviteit' WHERE Categorie = 'ict' AND Groep = '' AND Slug IN ('vpn', 'voip');
UPDATE Paginas SET Groep = 'Development' WHERE Categorie = 'ict' AND Groep = '' AND Slug IN ('websites', 'webshops');
UPDATE Paginas SET Groep = 'Marketing' WHERE Categorie = 'ict' AND Groep = '' AND Slug IN ('marketing-seo', 'linkbuilding');
