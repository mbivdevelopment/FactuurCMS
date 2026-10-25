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
('bedrijf.naam', '"Qevanto"'),
('bedrijf.adres', '"Industrieweg 1"'),
('bedrijf.postcode', '"3000 AA"'),
('bedrijf.plaats', '"Rotterdam"'),
('bedrijf.email', '"info@qevanto.nl"'),
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

-- Website: domeinextensies, megamenu-pagina's, blog, portfolio, kennisbank, logo's, team, tijdlijn en datacenters.
INSERT IGNORE INTO Producten (Id, Nummer, Groep, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp) VALUES
(7, 'A00007', 'Domeinen', '.com-domeinnaam', 'com-domeinnaam', 'Internationale .com-domeinnaam.', '["DNS-beheer","Gratis doorsturen","Privacybescherming"]', 1295, 'jaar', 0, 21.00, 0, 1, 7, NOW()),
(8, 'A00008', 'Domeinen', '.eu-domeinnaam', 'eu-domeinnaam', 'Europese .eu-domeinnaam.', '["DNS-beheer","Gratis doorsturen"]', 795, 'jaar', 0, 21.00, 0, 1, 8, NOW()),
(9, 'A00009', 'Domeinen', '.be-domeinnaam', 'be-domeinnaam', 'Belgische .be-domeinnaam.', '["DNS-beheer","Gratis doorsturen"]', 895, 'jaar', 0, 21.00, 0, 1, 9, NOW());

INSERT IGNORE INTO Paginas (Id, Categorie, Groep, Titel, Slug, Icoon, Samenvatting, Inhoud, ProductGroep, Volgorde, AangemaaktOp) VALUES
(1, 'cloud', 'Domeinen', 'Domeinnaam registreren', 'domeinnaam-registreren', 'globe', 'Claim jouw .nl, .com of .eu binnen een minuut.', 'Je domeinnaam is je adres op internet. Bij ons registreer je hem vlot, met volledig DNS-beheer en zonder verborgen kosten.\n\n## Wat je krijgt\n- Direct actief na betaling\n- Volledig DNS-beheer in het hostingpaneel\n- Gratis doorsturen naar een bestaande website\n- Persoonlijke support vanuit Nederland', 'Domeinen', 1, NOW()),
(2, 'cloud', 'Domeinen', 'Domein verhuizen', 'domein-verhuizen', 'refresh', 'Verhuis je domein zonder downtime naar ons.', 'Verhuizen gaat met een verhuiscode van je huidige provider. Wij regelen de rest en zorgen dat je website en e-mail gewoon bereikbaar blijven.\n\n## Zo werkt het\n- Vraag de verhuiscode op bij je huidige provider\n- Bestel de verhuizing bij ons\n- Wij zetten DNS en e-mail vooraf klaar', 'Domeinen', 2, NOW()),
(3, 'cloud', 'Domeinen', 'DNS-beheer', 'dns-beheer', 'server', 'Zelf records beheren, met DNSSEC.', 'Beheer A-, CNAME-, MX- en TXT-records zelf in ons hostingpaneel. Wijzigingen zijn binnen enkele minuten actief.', NULL, 3, NOW()),
(4, 'cloud', 'Hosting', 'Webhosting', 'webhosting', 'server', 'Snelle NVMe-hosting met gratis SSL.', 'Onze webhosting draait op snelle NVMe-servers met LiteSpeed, gratis SSL-certificaten en dagelijkse back-ups.\n\n## Altijd inbegrepen\n- Gratis SSL-certificaat\n- Dagelijkse back-ups\n- E-mail op je eigen domein\n- 99,9% uptime', 'Hosting', 1, NOW()),
(5, 'cloud', 'Hosting', 'WordPress hosting', 'wordpress-hosting', 'layout', 'Geoptimaliseerd voor WordPress en WooCommerce.', 'WordPress staat met één klik klaar. Met caching, automatische updates en staging-omgeving draait je site razendsnel.', 'Hosting', 2, NOW()),
(6, 'cloud', 'Hosting', 'E-mailhosting', 'e-mailhosting', 'mail', 'Zakelijke e-mail met spamfilter.', 'Professionele e-mail op je eigen domein, met webmail, spam- en virusfilter en ondersteuning voor alle apparaten.', NULL, 3, NOW()),
(7, 'cloud', 'Servers', 'VPS servers', 'vps-servers', 'cpu', 'Eigen virtuele server met root-toegang.', 'Een VPS geeft je gegarandeerde resources en volledige vrijheid. Kies je besturingssysteem en schaal op wanneer je wilt.', 'VPS', 1, NOW()),
(8, 'cloud', 'Servers', 'Dedicated servers', 'dedicated-servers', 'server', 'Een complete server, alleen voor jou.', 'Maximale prestaties en isolatie op hardware die volledig voor jou is. Wij beheren de server desgewenst volledig.', NULL, 2, NOW()),
(9, 'cloud', 'Servers', 'Cloud servers', 'cloud-servers', 'cloud', 'Flexibel schalen in onze datacenters.', 'Cloudservers die je per uur op- en afschaalt, in onze datacenters in de Verenigde Staten.', NULL, 3, NOW()),
(10, 'ict', 'Connectiviteit', 'VPN', 'vpn', 'lock', 'Veilig verbonden, waar je ook werkt.', 'Met een zakelijke VPN verbind je medewerkers en vestigingen veilig met je netwerk. Wij richten het in en beheren het.', NULL, 1, NOW()),
(11, 'ict', 'Connectiviteit', 'VoIP', 'voip', 'phone', 'Zakelijke telefonie via internet.', 'Bel via internet met je vaste nummer, op elk apparaat. Inclusief keuzemenu, wachtrijen en voicemail naar e-mail.', NULL, 2, NOW()),
(12, 'ict', 'Development', 'Websites', 'websites', 'layout', 'Op maat gemaakte websites in jouw huisstijl.', 'We ontwerpen en bouwen websites die snel laden, goed vindbaar zijn en eenvoudig zelf te beheren.', 'Websites', 3, NOW()),
(13, 'ict', 'Development', 'Webshops', 'webshops', 'cart', 'Webshops die verkopen, gekoppeld aan je systemen.', 'Van WooCommerce tot maatwerk: wij bouwen webshops met betaalkoppelingen, voorraadbeheer en verzendlabels.', NULL, 4, NOW()),
(14, 'ict', 'Marketing', 'Marketing & SEO', 'marketing-seo', 'trend', 'Beter gevonden worden in Google.', 'Technische SEO, content en campagnes die meetbaar resultaat opleveren. Met heldere rapportages.', NULL, 5, NOW()),
(15, 'ict', 'Marketing', 'Linkbuilding', 'linkbuilding', 'link', 'Kwalitatieve links voor meer autoriteit.', 'Duurzame linkbuilding via relevante, Nederlandstalige websites. Geen trucjes, wel resultaat.', NULL, 6, NOW());

INSERT IGNORE INTO BlogBerichten (Id, Titel, Slug, Categorie, Auteur, PublicatieDatum, Samenvatting, Inhoud, AangemaaktOp) VALUES
(1, 'Nieuw datacenter in Dallas live', 'nieuw-datacenter-in-dallas-live', 'Nieuws', 'Team Qevanto', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'We breiden uit: vanaf vandaag draaien servers ook vanuit Dallas, Texas.', 'Met onze nieuwe locatie in Dallas bieden we lagere latency voor klanten in Noord-Amerika.\n\n## Wat betekent dit voor jou?\n- Kies bij een nieuwe VPS de locatie Dallas\n- Bestaande diensten blijven ongewijzigd', NOW()),
(2, '5 tips voor een snellere WordPress-site', '5-tips-voor-een-snellere-wordpress-site', 'Tips', 'Team Qevanto', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'Met deze vijf aanpassingen laadt je WordPress-site merkbaar sneller.', '- Gebruik caching\n- Optimaliseer afbeeldingen\n- Houd plugins beperkt\n- Kies PHP 8.3\n- Gebruik hosting met NVMe-opslag', NOW()),
(3, 'Waarom SSL onmisbaar is', 'waarom-ssl-onmisbaar-is', 'Beveiliging', 'Team Qevanto', DATE_SUB(CURDATE(), INTERVAL 18 DAY), 'Een SSL-certificaat beschermt je bezoekers en helpt je vindbaarheid.', 'Browsers waarschuwen bezoekers bij sites zonder SSL. Bij ons is SSL altijd gratis inbegrepen.', NOW()),
(4, 'Zakelijk bellen via VoIP: zo stap je over', 'zakelijk-bellen-via-voip', 'ICT', 'Team Qevanto', DATE_SUB(CURDATE(), INTERVAL 27 DAY), 'Overstappen naar VoIP is eenvoudiger dan je denkt.', 'Je houdt je nummer, belt op elk apparaat en bespaart op kosten.', NOW()),
(5, 'Linkbuilding in 2026: kwaliteit boven kwantiteit', 'linkbuilding-in-2026', 'Marketing', 'Team Qevanto', DATE_SUB(CURDATE(), INTERVAL 40 DAY), 'Waar je op let bij duurzame linkbuilding.', 'Relevante links van betrouwbare sites wegen zwaarder dan ooit.', NOW());

INSERT IGNORE INTO PortfolioItems (Id, Titel, Slug, Klant, Categorie, Url, Samenvatting, Inhoud, Volgorde, AangemaaktOp) VALUES
(1, 'Webshop voor Bakkerij Jansen', 'webshop-bakkerij-jansen', 'Bakkerij Jansen', 'Development', NULL, 'Online bestellen en afhalen, gekoppeld aan de kassa.', 'Een WooCommerce-webshop met afhaalmomenten, iDEAL en koppeling met het kassasysteem.', 1, NOW()),
(2, 'Nieuwe website De Vries Webdesign', 'website-de-vries-webdesign', 'De Vries Webdesign', 'Development', NULL, 'Strakke portfolio-site met snelle hosting.', 'Een snelle, goed vindbare website op onze WordPress hosting.', 2, NOW()),
(3, 'VoIP-telefonie voor Autobedrijf Smit', 'voip-autobedrijf-smit', 'Autobedrijf Smit', 'ICT', NULL, 'Telefonie met keuzemenu voor drie vestigingen.', 'Alle vestigingen bellen nu via één centrale met wachtrijen en voicemail naar e-mail.', 3, NOW()),
(4, 'VPN-netwerk voor Accountantskantoor Bos', 'vpn-accountantskantoor-bos', 'Accountantskantoor Bos', 'ICT', NULL, 'Veilig thuiswerken voor 25 medewerkers.', 'Een beheerde VPN met tweestapsverificatie voor alle medewerkers.', 4, NOW()),
(5, 'SEO-traject voor Fietsenwinkel Rijwiel', 'seo-fietsenwinkel-rijwiel', 'Fietsenwinkel Rijwiel', 'Marketing', NULL, '+140% organisch verkeer in zes maanden.', 'Technische SEO, nieuwe content en linkbuilding zorgden voor groei.', 5, NOW());

INSERT IGNORE INTO KennisbankArtikelen (Id, Categorie, Titel, Slug, Samenvatting, Inhoud, Volgorde, AangemaaktOp) VALUES
(1, 'E-mail', 'E-mail instellen op je telefoon', 'e-mail-instellen-op-je-telefoon', 'Stap voor stap je zakelijke mail op iPhone of Android.', '## Gegevens\n- Inkomende server: mail.jouwdomein.nl (IMAP, poort 993)\n- Uitgaande server: mail.jouwdomein.nl (SMTP, poort 465)\n- Gebruikersnaam: je volledige e-mailadres', 1, NOW()),
(2, 'Domeinen', 'DNS-records aanpassen', 'dns-records-aanpassen', 'Zo wijzig je A-, CNAME- en MX-records.', 'Log in op het hostingpaneel, kies Domeinen en daarna DNS. Wijzigingen zijn binnen enkele minuten actief.', 2, NOW()),
(3, 'Domeinen', 'Verhuiscode opvragen', 'verhuiscode-opvragen', 'Je domein verhuizen naar een andere provider.', 'Je vraagt de verhuiscode aan in het klantportaal onder Mijn diensten.', 3, NOW()),
(4, 'Hosting', 'WordPress installeren met één klik', 'wordpress-installeren', 'Binnen twee minuten een WordPress-site.', 'Open in het hostingpaneel de app-installer en kies WordPress.', 4, NOW()),
(5, 'Hosting', 'Bestanden uploaden via FTP', 'bestanden-uploaden-via-ftp', 'Verbinden met FileZilla.', 'Gebruik als host ftp.jouwdomein.nl, poort 21 en je FTP-gegevens uit het hostingpaneel.', 5, NOW()),
(6, 'Facturatie', 'Facturen downloaden', 'facturen-downloaden', 'Al je facturen als PDF in het klantportaal.', 'Ga in het klantportaal naar Facturatie en klik op het downloadicoon.', 6, NOW());

INSERT IGNORE INTO Logos (Id, Groep, Naam, Afbeelding, Volgorde, AangemaaktOp) VALUES
(1, 'Webservers', 'Apache', 'img/logos/apache.svg', 1, NOW()), (2, 'Webservers', 'nginx', 'img/logos/nginx.svg', 2, NOW()), (3, 'Webservers', 'OpenLiteSpeed', 'img/logos/openlitespeed.svg', 3, NOW()),
(4, 'Webservers', 'Cloudflare', 'img/logos/cloudflare.svg', 4, NOW()), (5, 'Webservers', 'Let''s Encrypt', 'img/logos/letsencrypt.svg', 5, NOW()), (6, 'Webservers', 'Docker', 'img/logos/docker.svg', 6, NOW()),
(7, 'Talen & databases', 'PHP', 'img/logos/php.svg', 1, NOW()), (8, 'Talen & databases', 'Python', 'img/logos/python.svg', 2, NOW()), (9, 'Talen & databases', 'Node.js', 'img/logos/nodedotjs.svg', 3, NOW()),
(10, 'Talen & databases', 'MariaDB', 'img/logos/mariadb.svg', 4, NOW()), (11, 'Talen & databases', 'MySQL', 'img/logos/mysql.svg', 5, NOW()), (12, 'Talen & databases', 'Redis', 'img/logos/redis.svg', 6, NOW()),
(13, 'CMS & webshops', 'WordPress', 'img/logos/wordpress.svg', 1, NOW()), (14, 'CMS & webshops', 'Joomla', 'img/logos/joomla.svg', 2, NOW()), (15, 'CMS & webshops', 'Drupal', 'img/logos/drupal.svg', 3, NOW()),
(16, 'CMS & webshops', 'WooCommerce', 'img/logos/woocommerce.svg', 4, NOW()), (17, 'CMS & webshops', 'Magento', 'img/logos/magento.svg', 5, NOW()), (18, 'CMS & webshops', 'PrestaShop', 'img/logos/prestashop.svg', 6, NOW()), (19, 'CMS & webshops', 'Laravel', 'img/logos/laravel.svg', 7, NOW()),
(20, 'Besturingssystemen', 'AlmaLinux', 'img/logos/almalinux.svg', 1, NOW()), (21, 'Besturingssystemen', 'Ubuntu', 'img/logos/ubuntu.svg', 2, NOW()), (22, 'Besturingssystemen', 'Debian', 'img/logos/debian.svg', 3, NOW());

INSERT IGNORE INTO Teamleden (Id, Naam, Functie, Bio, Email, Volgorde, AangemaaktOp) VALUES
(1, 'Mark Beheer', 'Oprichter & directeur', 'Begon op zolder met een paar servers en bouwde Qevanto uit tot een complete ICT-partner.', 'info@qevanto.nl', 1, NOW()),
(2, 'Lisa de Jong', 'Klantenservice', 'Zorgt dat elke vraag snel en persoonlijk wordt beantwoord.', NULL, 2, NOW()),
(3, 'Tom Bakker', 'Systeembeheerder', 'Houdt onze servers en netwerken 24/7 in topconditie.', NULL, 3, NOW()),
(4, 'Sanne Visser', 'Webdeveloper', 'Bouwt websites en webshops die snel, mooi en vindbaar zijn.', NULL, 4, NOW());

INSERT IGNORE INTO Tijdlijn (Id, Jaar, Titel, Soort, Verhaal, Volgorde, AangemaaktOp) VALUES
(1, '2016', 'De eerste server', 'mijlpaal', 'Vanaf een zolderkamer hosten we de eerste websites voor vrienden en lokale ondernemers.', 1, NOW()),
(2, '2018', 'Qevanto is geboren', 'lancering', 'De hobby wordt een bedrijf: Qevanto gaat officieel van start met webhosting en domeinen.', 2, NOW()),
(3, '2020', 'ICT & Development', 'lancering', 'We breiden uit met websites, webshops, VPN en VoIP voor het mkb.', 3, NOW()),
(4, '2022', 'Overname hostingportefeuille', 'overname', 'We nemen de klanten van een regionale hostingpartij over en verwelkomen honderden nieuwe klanten.', 4, NOW()),
(5, '2024', 'Eigen hostingpaneel', 'lancering', 'Met ons eigen hostingpaneel beheren klanten alles op één plek.', 5, NOW()),
(6, '2026', 'Datacenters in de VS', 'mijlpaal', 'Met locaties in New York en Dallas zijn we ook in Noord-Amerika ijzersterk verbonden.', 6, NOW());

INSERT IGNORE INTO Datacenters (Id, Naam, Stad, Land, Latitude, Longitude, Omschrijving, Status, Volgorde, AangemaaktOp) VALUES
(1, 'NYC-1', 'New York', 'Verenigde Staten', 40.71280, -74.00600, 'Tier III-datacenter aan de oostkust, ideaal voor Europa en Noord-Amerika.', 'actief', 1, NOW()),
(2, 'DAL-1', 'Dallas', 'Verenigde Staten', 32.77670, -96.79700, 'Centraal gelegen in de VS met uitstekende verbindingen.', 'actief', 2, NOW());

INSERT IGNORE INTO Instellingen (Sleutel, Waarde) VALUES
('topbar.telefoon', '"085 123 4567"'),
('topbar.email', '"info@qevanto.nl"'),
('topbar.slogan', '"Vlot geregeld, ijzersterk verbonden."'),
('links.webmail', '"https://webmail.qevanto.nl"'),
('links.paneel', '"https://hosting.qevanto.nl"'),
('hero.titel', '"Jouw domeinnaam, vlot geregeld"'),
('hero.tekst', '"Zoek je nieuwe domeinnaam en start direct met hosting, e-mail en een website die ijzersterk verbonden is."'),
('hero.extensies', '".nl,.com,.eu,.be,.net,.org"'),
('over.titel', '"Qevanto: jouw ICT-partner"'),
('over.tekst', '"Qevanto regelt alles voor je online aanwezigheid: van domeinnaam en hosting tot website, webshop, VPN en telefonie. Persoonlijk, snel en zonder gedoe.\\n\\n- Persoonlijke support vanuit Nederland\\n- Eigen datacenters in New York en Dallas\\n- Alles onder één dak"'),
('over.wie', '"Wij zijn een team van hosting- en ICT-specialisten met een passie voor techniek die gewoon werkt. Wat begon op een zolderkamer, is uitgegroeid tot een complete ICT-partner voor het mkb."'),
('over.hoe', '"Wij zijn eerlijk, bereikbaar en no-nonsense. Je krijgt altijd iemand aan de lijn die je kent en die meedenkt."'),
('over.missie', '"Ondernemers ontzorgen met ICT die vlot geregeld en ijzersterk verbonden is, zodat zij zich kunnen richten op hun eigen werk."'),
('footer.tekst', '"Hosting, domeinen, servers en ICT voor ondernemers. Vlot geregeld, ijzersterk verbonden."');

INSERT IGNORE INTO PortfolioCategorieen (Naam, Volgorde, AangemaaktOp) VALUES ('Development', 1, NOW()), ('Marketing', 2, NOW()), ('ICT', 3, NOW());

-- De inhoud van deze migraties zit al in deze seed; zo worden ze niet nog eens uitgevoerd.
UPDATE Producten SET Dienstsoort = CASE Groep WHEN 'Hosting' THEN 'Webhosting' WHEN 'Domeinen' THEN 'Domeinnamen' WHEN 'VPS' THEN 'Cloud VPS' WHEN 'E-mail' THEN 'E-mail Hosting' WHEN 'Websites' THEN 'Informatieve Websites' ELSE NULL END WHERE Dienstsoort IS NULL;
INSERT IGNORE INTO Migraties (Naam, UitgevoerdOp) VALUES ('2026_10_23_001_website_basisinhoud.sql', NOW()), ('2026_10_23_002_ict_kolommen.sql', NOW()), ('2026_10_23_003_portfolio_categorieen_budget.sql', NOW()), ('2026_10_23_004_menu_indeling.sql', NOW()), ('2026_10_23_005_projecten_afspraken.sql', NOW()), ('2026_10_23_006_contracten_inkoop_storingen_antwoorden.sql', NOW()), ('2026_10_23_007_dienstsoort.sql', NOW()), ('2026_10_23_008_crediteuren_leads.sql', NOW()), ('2026_10_23_009_project_uurloon_taken.sql', NOW());

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

INSERT IGNORE INTO Projecten (Id, KlantId, Naam, Status, Facturatie, StartDatum, EindDatum, Budget, Uurtarief, Omschrijving, AangemaaktOp) VALUES
(1, 3, 'Nieuwe website Bakkerij Jansen', 'actief', 'budget', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 79500, 7500, 'Ontwerp en bouw van de nieuwe website met bestelmodule.', NOW()),
(2, 2, 'Hostingmigratie De Vries Webdesign', 'gepland', 'uurloon', DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 15000, 8500, 'Verhuizen van 12 websites naar onze servers.', NOW());
INSERT IGNORE INTO Afspraken (Id, KlantId, Onderwerp, Datum, Tijd, Locatie, Status, Notities, AangemaaktOp) VALUES
(1, 3, 'Kick-off website', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00', 'Bij de klant', 'gepland', 'Huisstijl en teksten meenemen.', NOW()),
(2, 2, 'Evaluatie hosting', DATE_ADD(CURDATE(), INTERVAL 9 DAY), '14:30', 'Online (Teams)', 'gepland', NULL, NOW());

INSERT INTO SnelleAntwoorden (Titel, Tekst, Volgorde, AangemaaktOp)
SELECT * FROM (SELECT 'Bedankt, we kijken ernaar' AS Titel, 'Bedankt voor je bericht. We gaan er direct mee aan de slag en laten je zo snel mogelijk weten wat de status is.' AS Tekst, 1 AS Volgorde, NOW() AS AangemaaktOp
UNION ALL SELECT 'Opgelost', 'Het probleem is opgelost. Kun je bevestigen dat alles weer naar behoren werkt? Dan sluiten we het ticket.', 2, NOW()
UNION ALL SELECT 'Meer informatie nodig', 'Om je goed te kunnen helpen hebben we wat meer informatie nodig. Kun je een screenshot sturen en aangeven wanneer het probleem begon?', 3, NOW()) d
WHERE NOT EXISTS (SELECT 1 FROM SnelleAntwoorden);

INSERT INTO Crediteuren (Bedrijfsnaam, Contactpersoon, Email, Telefoon, Iban, KvkNummer, BtwNummer, AangemaaktOp) VALUES
('Datacenter Partners B.V.', 'Peter de Groot', 'facturen@datacenterpartners.nl', '010 123 4567', 'NL91ABNA0417164300', '12345678', 'NL123456789B01', NOW()),
('Domeinregistrar Europa', NULL, 'billing@registrar.example', NULL, NULL, NULL, NULL, NOW());

INSERT INTO Leads (Naam, Bedrijf, Email, Telefoon, Bron, Status, Waarde, VolgendeActie, Notities, AangemaaktOp) VALUES
('Sophie Jansen', 'Jansen Advies', 'sophie@jansenadvies.nl', '06 12345678', 'website', 'nieuw', 150000, CURDATE() + INTERVAL 2 DAY, 'Wil een nieuwe website met hosting.', NOW()),
('Ruben Bakker', 'Bakker Logistiek', 'ruben@bakkerlogistiek.nl', NULL, 'doorverwijzing', 'offerte', 480000, CURDATE() + INTERVAL 7 DAY, 'Offerte voor VPN en werkplekbeheer verstuurd.', NOW());

INSERT IGNORE INTO ProjectTaken (Id, ProjectId, Titel, Status, MedewerkerId, Deadline, GeschatteUren, GewerkteUren, AangemaaktOp) VALUES
(1, 1, 'Ontwerp homepage', 'klaar', 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 4.00, 4.50, NOW()),
(2, 1, 'Bestelmodule bouwen', 'bezig', 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 6.00, 2.25, NOW()),
(3, 1, 'Teksten en foto''s plaatsen', 'open', NULL, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 2.00, 0, NOW()),
(4, 2, 'Websites inventariseren', 'open', 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 1.50, 0, NOW());
