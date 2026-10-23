-- FactuurCMS demodata. Importeer na schema.sql.
-- Logins: beheer@factuurcms.nl / Beheer123!  ·  klant@factuurcms.nl / Klant123!

INSERT INTO Gebruikers (Id, Nummer, Rol, Email, Wachtwoord, Voornaam, Achternaam, Bedrijfsnaam, Telefoon, Adres, Postcode, Plaats, Land, KvkNummer, BtwNummer, Actief, AangemaaktOp) VALUES
(1, 'M00001', 'beheerder', 'beheer@factuurcms.nl', '$2y$10$ASITHgnuhFtU9ZLlDH2AGuSoKkhtSrhNgExw8u0/9c5FiCiR2EyQS', 'Mark', 'Beheer', NULL, '010-1234567', NULL, NULL, NULL, 'NL', NULL, NULL, 1, NOW()),
(2, 'K00001', 'klant', 'klant@factuurcms.nl', '$2y$10$yLaw9Cu74nyd1UEhfTCoMu3hIP5S5H2hzia.QCwLd44lP5JgaKQNq', 'Sanne', 'de Vries', 'De Vries Webdesign', '06-12345678', 'Stationsplein 12', '3011 AB', 'Rotterdam', 'NL', '12345678', 'NL001234567B01', 1, NOW()),
(3, 'K00002', 'klant', 'info@bakkerijjansen.nl', '$2y$10$yLaw9Cu74nyd1UEhfTCoMu3hIP5S5H2hzia.QCwLd44lP5JgaKQNq', 'Peter', 'Jansen', 'Bakkerij Jansen', '06-87654321', 'Dorpsstraat 4', '4811 CD', 'Breda', 'NL', '87654321', NULL, 1, NOW());

INSERT INTO Producten (Id, Nummer, Groep, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp) VALUES
(1, 'A00001', 'Hosting', 'Webhosting Start', 'webhosting-start', 'Ideaal voor een eerste website of blog.', '["5 GB NVMe-opslag","1 website","5 e-mailadressen","Gratis SSL-certificaat","Dagelijkse back-ups"]', 395, 'maand', 0, 21.00, 0, 1, 1, NOW()),
(2, 'A00002', 'Hosting', 'Webhosting Pro', 'webhosting-pro', 'Voor bedrijfswebsites en webshops.', '["25 GB NVMe-opslag","10 websites","Onbeperkt e-mail","Gratis SSL-certificaat","Dagelijkse back-ups","Staging-omgeving"]', 795, 'maand', 0, 21.00, 1, 1, 2, NOW()),
(3, 'A00003', 'Hosting', 'Webhosting Business', 'webhosting-business', 'Maximale prestaties voor drukbezochte sites.', '["100 GB NVMe-opslag","Onbeperkt websites","Onbeperkt e-mail","Dedicated resources","Prioriteit support"]', 1495, 'maand', 0, 21.00, 0, 1, 3, NOW()),
(4, 'A00004', 'Domeinen', '.nl-domeinnaam', 'nl-domeinnaam', 'Registratie of verhuizing van een .nl-domein.', '["Inclusief DNS-beheer","Gratis verhuizen","Doorsturen naar elk adres"]', 995, 'jaar', 0, 21.00, 0, 1, 4, NOW()),
(5, 'A00005', 'VPS', 'VPS Basic', 'vps-basic', 'Eigen virtuele server met root-toegang.', '["2 vCPU","4 GB RAM","80 GB NVMe","1 Gbit/s netwerk","AlmaLinux of Ubuntu"]', 1995, 'maand', 2500, 21.00, 0, 1, 5, NOW()),
(6, 'A00006', 'Websites', 'Website laten maken', 'website-laten-maken', 'Professionele website in jouw huisstijl.', '["Responsive design","CMS inbegrepen","SEO-basis","Oplevering binnen 3 weken"]', 79500, 'eenmalig', 0, 21.00, 0, 1, 6, NOW());

INSERT INTO Diensten (Id, Nummer, KlantId, ProductId, Naam, Omschrijving, Prijs, BtwTarief, Periode, Status, StartDatum, VolgendeFactuur, AangemaaktOp) VALUES
(1, 'D00001', 2, 2, 'Webhosting Pro', 'devrieswebdesign.nl', 795, 21.00, 'maand', 'actief', DATE_SUB(CURDATE(), INTERVAL 2 MONTH), DATE_ADD(CURDATE(), INTERVAL 5 DAY), NOW()),
(2, 'D00002', 2, 4, '.nl-domeinnaam', 'devrieswebdesign.nl', 995, 21.00, 'jaar', 'actief', DATE_SUB(CURDATE(), INTERVAL 2 MONTH), DATE_ADD(CURDATE(), INTERVAL 10 MONTH), NOW()),
(3, 'D00003', 3, 1, 'Webhosting Start', 'bakkerijjansen.nl', 395, 21.00, 'maand', 'actief', DATE_SUB(CURDATE(), INTERVAL 1 MONTH), CURDATE(), NOW());

INSERT INTO Documenten (Id, Soort, Nummer, KlantId, Status, Datum, VervalDatum, Onderwerp, Subtotaal, Btw, Totaal, Betaald, BetaaldOp, VerzondenOp, AangemaaktOp) VALUES
(1, 'factuur', 'F00001', 2, 'betaald', DATE_SUB(CURDATE(), INTERVAL 40 DAY), DATE_SUB(CURDATE(), INTERVAL 26 DAY), 'Hosting en domein', 1790, 376, 2166, 2166, DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 40 DAY), NOW()),
(2, 'factuur', 'F00002', 2, 'open', DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 9 DAY), 'Webhosting Pro', 795, 167, 962, 0, NULL, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW()),
(3, 'factuur', 'F00003', 3, 'open', DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'Webhosting Start', 395, 83, 478, 0, NULL, DATE_SUB(NOW(), INTERVAL 20 DAY), NOW()),
(4, 'offerte', 'O00001', 3, 'verzonden', DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 27 DAY), 'Nieuwe website Bakkerij Jansen', 79500, 16695, 96195, 0, NULL, DATE_SUB(NOW(), INTERVAL 3 DAY), NOW());

INSERT INTO DocumentRegels (DocumentId, DienstId, Omschrijving, Aantal, Prijs, BtwTarief, Volgorde) VALUES
(1, 1, 'Webhosting Pro - devrieswebdesign.nl', 1.00, 795, 21.00, 0),
(1, 2, '.nl-domeinnaam - devrieswebdesign.nl', 1.00, 995, 21.00, 1),
(2, 1, 'Webhosting Pro - devrieswebdesign.nl', 1.00, 795, 21.00, 0),
(3, 3, 'Webhosting Start - bakkerijjansen.nl', 1.00, 395, 21.00, 0),
(4, NULL, 'Website laten maken (responsive, CMS, SEO-basis)', 1.00, 79500, 21.00, 0);

INSERT INTO Tickets (Id, Nummer, KlantId, DienstId, Onderwerp, Afdeling, Prioriteit, Status, LaatsteReactie, AangemaaktOp) VALUES
(1, 'T00001', 2, 1, 'E-mail komt niet aan op info@', 'Support', 'hoog', 'open', NOW(), NOW()),
(2, 'T00002', 3, NULL, 'Vraag over factuur F00003', 'Facturatie', 'normaal', 'beantwoord', NOW(), NOW());

INSERT INTO TicketBerichten (TicketId, GebruikerId, Bericht, Intern, AangemaaktOp) VALUES
(1, 2, 'Sinds vanochtend komen mails naar info@devrieswebdesign.nl niet meer binnen. Kunnen jullie kijken?', 0, NOW()),
(2, 3, 'Kan ik de factuur in twee termijnen betalen?', 0, NOW()),
(2, 1, 'Dat is prima, betaal de eerste helft binnen 14 dagen.', 0, NOW());

INSERT INTO Instellingen (Sleutel, Waarde) VALUES
('bedrijf.naam', '"MBIV Hosting"'),
('bedrijf.adres', '"Industrieweg 1"'),
('bedrijf.postcode', '"3000 AA"'),
('bedrijf.plaats', '"Rotterdam"'),
('bedrijf.email', '"info@mbivhosting.nl"'),
('bedrijf.telefoon', '"010-1234567"'),
('bedrijf.kvk', '"12345678"'),
('bedrijf.btw', '"NL001234567B01"'),
('bedrijf.iban', '"NL00BANK0123456789"'),
('branding.kleur', '"#ff5a1f"'),
('facturatie.betaaltermijn', '14'),
('facturatie.offerte_geldig', '30'),
('facturatie.btw', '21'),
('facturatie.dagen_vooraf', '7'),
('facturatie.voettekst', '"Gelieve het bedrag binnen de betaaltermijn over te maken o.v.v. het factuurnummer."'),
('website.titel', '"Snelle en betrouwbare hosting"'),
('website.intro', '"Webhosting, domeinen en VPS-servers met persoonlijke support vanuit Nederland."'),
('mail.methode', '"log"'),
('updater.repo', '"mbivdevelopment/FactuurCMS"'),
('updater.branch', '"main"');
