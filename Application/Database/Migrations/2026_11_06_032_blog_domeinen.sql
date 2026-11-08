-- Blogberichten over domeinnamen (categorie Domeinen) voor de carrousel op /domeinen.
UPDATE BlogBerichten SET Categorie = 'Domeinen' WHERE Slug = 'domein-verhuizen-zonder-downtime';
INSERT IGNORE INTO BlogBerichten (Titel, Slug, Categorie, Auteur, PublicatieDatum, Samenvatting, Inhoud, Zichtbaar, AangemaaktOp) VALUES
('Welke domeinextensie kies je: .nl, .com of .eu?', 'welke-domeinextensie-kiezen', 'Domeinen', 'MBIV Development', CURDATE(),
'De extensie bepaalt hoe klanten je zien en vinden. Zo kies je de juiste tussen .nl, .com, .eu en de nieuwe extensies zoals .shop en .cloud.',
'## Begin bij je doelgroep

Richt je je op Nederlandse klanten, dan is .nl de logische keuze: vertrouwd, goedkoop en sterk in Nederlandse zoekresultaten. Werk je internationaal, dan is .com nog altijd de standaard die iedereen kent.

## .eu en landextensies

Een .eu straalt Europese aanwezigheid uit en is handig als je in meerdere EU-landen actief bent. Verkoop je gericht in België of Duitsland, registreer dan ook .be of .de en stuur die door naar je hoofdsite.

## Nieuwe extensies

- **.shop** en **.store** voor webshops
- **.cloud**, **.tech** en **.dev** voor IT-bedrijven
- **.amsterdam** en **.frl** voor lokale zichtbaarheid

## Ons advies

Registreer je hoofdextensie plus de belangrijkste alternatieven. Zo voorkom je dat een concurrent met jouw naam aan de haal gaat, en je stuurt alles eenvoudig door naar één website.', 1, NOW()),
('Bescherm je merknaam: registreer meerdere extensies', 'merknaam-beschermen-extensies', 'Domeinen', 'MBIV Development', CURDATE(),
'Een merknaam is meer waard dan één domein. Met een paar extra extensies en typefoutvarianten houd je concurrenten en phishers buiten de deur.',
'## Waarom één domein niet genoeg is

Zodra je bedrijf bekend wordt, registreren anderen graag varianten van je naam: een andere extensie, een streepje ertussen of een veelgemaakte typefout. Dat kost je bezoekers en kan je reputatie schaden als er phishing mee wordt gedaan.

## Wat registreer je?

- Je hoofdnaam in .nl, .com en .eu
- Veelvoorkomende typefouten en varianten met en zonder streepje
- Branche-extensies zoals .shop of .cloud als die bij je passen

## Doorsturen naar je hoofdsite

Alle extra domeinen stuur je met een 301-redirect door naar je hoofddomein. Dat is gratis bij elk domein in je klantportaal en kost je geen extra hosting.

## Automatisch verlengen

Zet automatische verlenging aan voor al je domeinen. Een vergeten verlenging is de meest voorkomende manier waarop bedrijven hun merknaam alsnog verliezen.', 1, NOW()),
('DNS-records uitgelegd: A, CNAME, MX en TXT', 'dns-records-uitgelegd', 'Domeinen', 'MBIV Development', CURDATE(),
'DNS is het telefoonboek van internet. In dit artikel leggen we de belangrijkste recordtypes uit en wanneer je welk record nodig hebt.',
'## Wat doet DNS?

DNS vertaalt je domeinnaam naar het IP-adres van de server waar je website of e-mail draait. Elke wijziging in je DNS-zone bepaalt dus waar bezoekers en e-mails terechtkomen.

## De belangrijkste records

- **A-record** – koppelt een naam aan een IPv4-adres. Dit is het record voor je website.
- **AAAA-record** – hetzelfde, maar voor IPv6.
- **CNAME** – een alias naar een andere naam, bijvoorbeeld www naar je hoofddomein.
- **MX** – bepaalt welke server je e-mail ontvangt.
- **TXT** – vrije tekst, gebruikt voor SPF, DKIM, DMARC en domeinverificatie.

## TTL

De TTL (time to live) bepaalt hoe lang providers een record onthouden. Verlaag de TTL een dag vóór een wijziging naar 300 seconden, zodat de nieuwe waarde snel actief is.

## Zelf beheren

In je klantportaal beheer je alle recordtypes zelf. Wijzigingen zijn doorgaans binnen enkele minuten wereldwijd actief.', 1, NOW()),
('WHOIS-privacy: wat is zichtbaar van jou bij een domein?', 'whois-privacy-uitgelegd', 'Domeinen', 'MBIV Development', CURDATE(),
'Bij elke domeinregistratie worden houdergegevens vastgelegd. Welke daarvan openbaar zijn, verschilt per extensie — en zo houd je het privé.',
'## Wat is WHOIS?

WHOIS is het openbare register van domeinhouders. Vroeger stonden daar naam, adres, telefoonnummer en e-mailadres van elke houder in. Sinds de AVG is dat sterk beperkt, maar niet voor elke extensie gelijk.

## Per extensie anders

- **.nl** – SIDN toont alleen de registrar en de nameservers; houdergegevens zijn afgeschermd.
- **.com / .net** – houdergegevens zijn standaard afgeschermd via privacybescherming die wij voor je activeren.
- **.eu** – particulieren zijn afgeschermd, bedrijven tonen bedrijfsnaam en land.

## Waarom het ertoe doet

Openbare gegevens trekken spam en phishing aan, zoals valse verlengingsfacturen. Afgeschermde WHOIS-gegevens voorkomen dat.

## Wat wij doen

Bij elk domein dat je bij ons registreert staat privacybescherming aan waar de extensie dat toelaat, zonder extra kosten. Je eigen gegevens blijven altijd zichtbaar in je klantportaal, zodat je het domein kunt blijven aantonen.', 1, NOW());
