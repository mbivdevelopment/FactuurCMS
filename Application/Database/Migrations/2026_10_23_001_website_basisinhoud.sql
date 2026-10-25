-- Basisinhoud voor de website op bestaande installaties: megamenu-pagina's, kennisbank, logo's, datacenters,
-- domeinextensies en standaardteksten. Bestaande gegevens en instellingen blijven ongemoeid.

INSERT IGNORE INTO Paginas (Categorie, Groep, Titel, Slug, Icoon, Samenvatting, Inhoud, ProductGroep, Volgorde, AangemaaktOp) VALUES
('cloud', 'Domeinen', 'Domeinnaam registreren', 'domeinnaam-registreren', 'globe', 'Claim jouw .nl, .com of .eu binnen een minuut.', 'Je domeinnaam is je adres op internet. Bij ons registreer je hem vlot, met volledig DNS-beheer en zonder verborgen kosten.\n\n## Wat je krijgt\n- Direct actief na betaling\n- Volledig DNS-beheer in het hostingpaneel\n- Gratis doorsturen naar een bestaande website\n- Persoonlijke support vanuit Nederland', 'Domeinen', 1, NOW()),
('cloud', 'Domeinen', 'Domein verhuizen', 'domein-verhuizen', 'refresh', 'Verhuis je domein zonder downtime naar ons.', 'Verhuizen gaat met een verhuiscode van je huidige provider. Wij regelen de rest en zorgen dat je website en e-mail gewoon bereikbaar blijven.\n\n## Zo werkt het\n- Vraag de verhuiscode op bij je huidige provider\n- Bestel de verhuizing bij ons\n- Wij zetten DNS en e-mail vooraf klaar', 'Domeinen', 2, NOW()),
('cloud', 'Domeinen', 'DNS-beheer', 'dns-beheer', 'server', 'Zelf records beheren, met DNSSEC.', 'Beheer A-, CNAME-, MX- en TXT-records zelf in ons hostingpaneel. Wijzigingen zijn binnen enkele minuten actief.', NULL, 3, NOW()),
('cloud', 'Hosting', 'Webhosting', 'webhosting', 'server', 'Snelle NVMe-hosting met gratis SSL.', 'Onze webhosting draait op snelle NVMe-servers met LiteSpeed, gratis SSL-certificaten en dagelijkse back-ups.\n\n## Altijd inbegrepen\n- Gratis SSL-certificaat\n- Dagelijkse back-ups\n- E-mail op je eigen domein\n- 99,9% uptime', 'Hosting', 1, NOW()),
('cloud', 'Hosting', 'WordPress hosting', 'wordpress-hosting', 'layout', 'Geoptimaliseerd voor WordPress en WooCommerce.', 'WordPress staat met één klik klaar. Met caching, automatische updates en staging-omgeving draait je site razendsnel.', 'Hosting', 2, NOW()),
('cloud', 'Hosting', 'E-mailhosting', 'e-mailhosting', 'mail', 'Zakelijke e-mail met spamfilter.', 'Professionele e-mail op je eigen domein, met webmail, spam- en virusfilter en ondersteuning voor alle apparaten.', NULL, 3, NOW()),
('cloud', 'Servers', 'VPS servers', 'vps-servers', 'cpu', 'Eigen virtuele server met root-toegang.', 'Een VPS geeft je gegarandeerde resources en volledige vrijheid. Kies je besturingssysteem en schaal op wanneer je wilt.', 'VPS', 1, NOW()),
('cloud', 'Servers', 'Dedicated servers', 'dedicated-servers', 'server', 'Een complete server, alleen voor jou.', 'Maximale prestaties en isolatie op hardware die volledig voor jou is. Wij beheren de server desgewenst volledig.', NULL, 2, NOW()),
('cloud', 'Servers', 'Cloud servers', 'cloud-servers', 'cloud', 'Flexibel schalen in onze datacenters.', 'Cloudservers die je per uur op- en afschaalt, in onze datacenters in de Verenigde Staten.', NULL, 3, NOW()),
('ict', '', 'VPN', 'vpn', 'lock', 'Veilig verbonden, waar je ook werkt.', 'Met een zakelijke VPN verbind je medewerkers en vestigingen veilig met je netwerk. Wij richten het in en beheren het.', NULL, 1, NOW()),
('ict', '', 'VoIP', 'voip', 'phone', 'Zakelijke telefonie via internet.', 'Bel via internet met je vaste nummer, op elk apparaat. Inclusief keuzemenu, wachtrijen en voicemail naar e-mail.', NULL, 2, NOW()),
('ict', '', 'Websites', 'websites', 'layout', 'Op maat gemaakte websites in jouw huisstijl.', 'We ontwerpen en bouwen websites die snel laden, goed vindbaar zijn en eenvoudig zelf te beheren.', 'Websites', 3, NOW()),
('ict', '', 'Webshops', 'webshops', 'cart', 'Webshops die verkopen, gekoppeld aan je systemen.', 'Van WooCommerce tot maatwerk: wij bouwen webshops met betaalkoppelingen, voorraadbeheer en verzendlabels.', NULL, 4, NOW()),
('ict', '', 'Marketing & SEO', 'marketing-seo', 'trend', 'Beter gevonden worden in Google.', 'Technische SEO, content en campagnes die meetbaar resultaat opleveren. Met heldere rapportages.', NULL, 5, NOW()),
('ict', '', 'Linkbuilding', 'linkbuilding', 'link', 'Kwalitatieve links voor meer autoriteit.', 'Duurzame linkbuilding via relevante, Nederlandstalige websites. Geen trucjes, wel resultaat.', NULL, 6, NOW());

INSERT IGNORE INTO KennisbankArtikelen (Categorie, Titel, Slug, Samenvatting, Inhoud, Volgorde, AangemaaktOp) VALUES
('E-mail', 'E-mail instellen op je telefoon', 'e-mail-instellen-op-je-telefoon', 'Stap voor stap je zakelijke mail op iPhone of Android.', '## Gegevens\n- Inkomende server: mail.jouwdomein.nl (IMAP, poort 993)\n- Uitgaande server: mail.jouwdomein.nl (SMTP, poort 465)\n- Gebruikersnaam: je volledige e-mailadres', 1, NOW()),
('Domeinen', 'DNS-records aanpassen', 'dns-records-aanpassen', 'Zo wijzig je A-, CNAME- en MX-records.', 'Log in op het hostingpaneel, kies Domeinen en daarna DNS. Wijzigingen zijn binnen enkele minuten actief.', 2, NOW()),
('Domeinen', 'Verhuiscode opvragen', 'verhuiscode-opvragen', 'Je domein verhuizen naar een andere provider.', 'Je vraagt de verhuiscode aan in het klantportaal onder Mijn diensten.', 3, NOW()),
('Hosting', 'WordPress installeren met één klik', 'wordpress-installeren', 'Binnen twee minuten een WordPress-site.', 'Open in het hostingpaneel de app-installer en kies WordPress.', 4, NOW()),
('Hosting', 'Bestanden uploaden via FTP', 'bestanden-uploaden-via-ftp', 'Verbinden met FileZilla.', 'Gebruik als host ftp.jouwdomein.nl, poort 21 en je FTP-gegevens uit het hostingpaneel.', 5, NOW()),
('Facturatie', 'Facturen downloaden', 'facturen-downloaden', 'Al je facturen als PDF in het klantportaal.', 'Ga in het klantportaal naar Facturatie en klik op het downloadicoon.', 6, NOW());

INSERT IGNORE INTO Logos (Groep, Naam, Afbeelding, Volgorde, AangemaaktOp) VALUES
('Webservers', 'Apache', 'img/logos/apache.svg', 1, NOW()), ('Webservers', 'nginx', 'img/logos/nginx.svg', 2, NOW()), ('Webservers', 'OpenLiteSpeed', 'img/logos/openlitespeed.svg', 3, NOW()),
('Webservers', 'Cloudflare', 'img/logos/cloudflare.svg', 4, NOW()), ('Webservers', 'Let''s Encrypt', 'img/logos/letsencrypt.svg', 5, NOW()), ('Webservers', 'Docker', 'img/logos/docker.svg', 6, NOW()),
('Talen & databases', 'PHP', 'img/logos/php.svg', 1, NOW()), ('Talen & databases', 'Python', 'img/logos/python.svg', 2, NOW()), ('Talen & databases', 'Node.js', 'img/logos/nodedotjs.svg', 3, NOW()),
('Talen & databases', 'MariaDB', 'img/logos/mariadb.svg', 4, NOW()), ('Talen & databases', 'MySQL', 'img/logos/mysql.svg', 5, NOW()), ('Talen & databases', 'Redis', 'img/logos/redis.svg', 6, NOW()),
('CMS & webshops', 'WordPress', 'img/logos/wordpress.svg', 1, NOW()), ('CMS & webshops', 'Joomla', 'img/logos/joomla.svg', 2, NOW()), ('CMS & webshops', 'Drupal', 'img/logos/drupal.svg', 3, NOW()),
('CMS & webshops', 'WooCommerce', 'img/logos/woocommerce.svg', 4, NOW()), ('CMS & webshops', 'Magento', 'img/logos/magento.svg', 5, NOW()), ('CMS & webshops', 'PrestaShop', 'img/logos/prestashop.svg', 6, NOW()), ('CMS & webshops', 'Laravel', 'img/logos/laravel.svg', 7, NOW()),
('Besturingssystemen', 'AlmaLinux', 'img/logos/almalinux.svg', 1, NOW()), ('Besturingssystemen', 'Ubuntu', 'img/logos/ubuntu.svg', 2, NOW()), ('Besturingssystemen', 'Debian', 'img/logos/debian.svg', 3, NOW());

INSERT IGNORE INTO Datacenters (Naam, Stad, Land, Latitude, Longitude, Omschrijving, Status, Volgorde, AangemaaktOp) VALUES
('NYC-1', 'New York', 'Verenigde Staten', 40.71280, -74.00600, 'Tier III-datacenter aan de oostkust, ideaal voor Europa en Noord-Amerika.', 'actief', 1, NOW()),
('DAL-1', 'Dallas', 'Verenigde Staten', 32.77670, -96.79700, 'Centraal gelegen in de VS met uitstekende verbindingen.', 'actief', 2, NOW());

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

INSERT INTO Producten (Nummer, Groep, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD(COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) + 1, 5, '0')), 'Domeinen', '.com-domeinnaam', 'com-domeinnaam', 'Internationale .com-domeinnaam.', '["DNS-beheer","Gratis doorsturen"]', 1295, 'jaar', 0, 21.00, 0, 1, 50, NOW()
FROM Producten WHERE Nummer REGEXP '^A[0-9]+$' AND NOT EXISTS (SELECT 1 FROM (SELECT Slug FROM Producten) p WHERE p.Slug = 'com-domeinnaam');

INSERT INTO Producten (Nummer, Groep, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD(COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) + 1, 5, '0')), 'Domeinen', '.eu-domeinnaam', 'eu-domeinnaam', 'Europese .eu-domeinnaam.', '["DNS-beheer","Gratis doorsturen"]', 795, 'jaar', 0, 21.00, 0, 1, 51, NOW()
FROM Producten WHERE Nummer REGEXP '^A[0-9]+$' AND NOT EXISTS (SELECT 1 FROM (SELECT Slug FROM Producten) p WHERE p.Slug = 'eu-domeinnaam');

INSERT INTO Producten (Nummer, Groep, Naam, Slug, Omschrijving, Kenmerken, Prijs, Periode, Instelkosten, BtwTarief, Uitgelicht, Zichtbaar, Volgorde, AangemaaktOp)
SELECT CONCAT('A', LPAD(COALESCE(MAX(CAST(SUBSTRING(Nummer, 2) AS UNSIGNED)), 0) + 1, 5, '0')), 'Domeinen', '.be-domeinnaam', 'be-domeinnaam', 'Belgische .be-domeinnaam.', '["DNS-beheer","Gratis doorsturen"]', 895, 'jaar', 0, 21.00, 0, 1, 52, NOW()
FROM Producten WHERE Nummer REGEXP '^A[0-9]+$' AND NOT EXISTS (SELECT 1 FROM (SELECT Slug FROM Producten) p WHERE p.Slug = 'be-domeinnaam');
