-- Nieuwe indeling van de megamenu's. Pagina's die niet meer in het menu staan blijven bereikbaar (Categorie 'overig').
UPDATE Paginas SET Categorie = 'overig' WHERE Slug IN ('domein-verhuizen', 'dns-beheer', 'wordpress-hosting', 'cloud-servers');

UPDATE Paginas SET Categorie = 'cloud', Groep = 'Domeinen', Titel = 'Domeinnaam Registreren', Volgorde = 1 WHERE Slug = 'domeinnaam-registreren';
UPDATE Paginas SET Categorie = 'cloud', Groep = 'Hosting', Titel = 'Webhosting', Volgorde = 1 WHERE Slug = 'webhosting';
UPDATE Paginas SET Categorie = 'cloud', Groep = 'Hosting', Titel = 'Email Hosting', Icoon = 'mail', Volgorde = 3 WHERE Slug = 'e-mailhosting';
UPDATE Paginas SET Categorie = 'cloud', Groep = 'Servers', Titel = 'Cloud VPS', Icoon = 'cloud', Samenvatting = 'Schaalbare VPS met root-toegang.', Volgorde = 1 WHERE Slug = 'vps-servers';
UPDATE Paginas SET Categorie = 'cloud', Groep = 'Servers', Titel = 'Dedicated Server', Volgorde = 2 WHERE Slug = 'dedicated-servers';
UPDATE Paginas SET Categorie = 'ict', Groep = 'Connectiviteit', Titel = 'VPN', Volgorde = 1 WHERE Slug = 'vpn';
UPDATE Paginas SET Categorie = 'ict', Groep = 'Connectiviteit', Titel = 'VoIP', Volgorde = 3 WHERE Slug = 'voip';
UPDATE Paginas SET Categorie = 'ict', Groep = 'Development', Titel = 'Informatieve Websites', Samenvatting = 'Websites in jouw huisstijl.', Volgorde = 1 WHERE Slug = 'websites';
UPDATE Paginas SET Categorie = 'ict', Groep = 'Development', Titel = 'Webshops', Samenvatting = 'Webshops die verkopen.', Volgorde = 2 WHERE Slug = 'webshops';
UPDATE Paginas SET Categorie = 'ict', Groep = 'Marketing', Titel = 'Marketing SEO', Volgorde = 1 WHERE Slug = 'marketing-seo';
UPDATE Paginas SET Categorie = 'ict', Groep = 'Marketing', Titel = 'Linkbuilding', Samenvatting = 'Kwalitatieve links, meer autoriteit.', Volgorde = 3 WHERE Slug = 'linkbuilding';

INSERT IGNORE INTO Paginas (Categorie, Groep, Titel, Slug, Icoon, Samenvatting, Inhoud, ProductGroep, Volgorde, AangemaaktOp) VALUES
('cloud', 'Domeinen', 'SSL Certificaten', 'ssl-certificaten', 'shield', 'Beveilig je website met SSL.', 'Met een SSL-certificaat versleutel je het verkeer tussen je website en je bezoekers. Browsers tonen het slotje en bezoekers vertrouwen je site.\n\n## Wat je krijgt\n- Gratis Let''s Encrypt bij al onze hosting\n- Uitgebreide certificaten voor webshops en organisaties\n- Automatische verlenging', NULL, 2, NOW()),
('cloud', 'Hosting', 'Reseller Hosting', 'reseller-hosting', 'users', 'Host websites voor je eigen klanten.', 'Met resellerhosting bied je zelf hosting aan je klanten, onder je eigen naam. Wij zorgen voor snelle servers en techniek, jij voor je klanten.\n\n## Altijd inbegrepen\n- Eigen klantaccounts beheren\n- Gratis SSL voor alle websites\n- Dagelijkse back-ups', 'Hosting', 2, NOW()),
('cloud', 'Servers', 'Big Storage', 'big-storage', 'box', 'Veel opslag voor back-ups en data.', 'Big Storage-servers bieden veel opslagruimte tegen een lage prijs, ideaal voor back-ups, archieven en grote bestanden.', NULL, 3, NOW()),
('ict', 'Connectiviteit', 'Werkplek beheer', 'werkplek-beheer', 'headset', 'Wij beheren je werkplekken.', 'Wij beheren laptops, computers en telefoons van je medewerkers: updates, beveiliging, back-ups en hulp op afstand.', NULL, 2, NOW()),
('ict', 'Connectiviteit', 'Licenties', 'licenties', 'file', 'Microsoft 365 en andere licenties.', 'Wij regelen en beheren je softwarelicenties, zoals Microsoft 365, zodat je altijd de juiste licenties hebt en niet te veel betaalt.', NULL, 4, NOW()),
('ict', 'Development', 'API Koppelingen', 'api-koppelingen', 'code', 'Systemen die automatisch samenwerken.', 'Wij koppelen je website, webshop, boekhouding en andere systemen via API''s, zodat gegevens automatisch worden uitgewisseld.', NULL, 3, NOW()),
('ict', 'Marketing', 'Social Media Onderhoud', 'social-media-onderhoud', 'chat', 'Wij houden je socials actief.', 'Wij plaatsen regelmatig berichten op je social media en reageren op je volgers, in jouw stijl en met jouw boodschap.', NULL, 2, NOW());
