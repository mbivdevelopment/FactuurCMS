-- E-mailhosting-artikelen voor de e-mailhostingpagina.
INSERT IGNORE INTO BlogBerichten (Titel, Slug, Categorie, Auteur, PublicatieDatum, Samenvatting, Inhoud, Zichtbaar, AangemaaktOp) VALUES
('Zakelijke e-mail op je eigen domeinnaam', 'zakelijke-email-op-eigen-domeinnaam', 'E-mail Hosting', 'MBIV Development', CURDATE(),
'Waarom een professioneel e-mailadres op je eigen domeinnaam vertrouwen en herkenbaarheid geeft.',
'## Een professionele eerste indruk

Een e-mailadres op je eigen domeinnaam laat zien dat je organisatie professioneel werkt. Klanten herkennen direct van wie een bericht afkomstig is en berichten blijven onderdeel van je eigen merk.

## Overal bereikbaar

Met e-mailhosting van Qevanto gebruik je je mailbox op computer, telefoon, tablet en via webmail. Je berichten, mappen en contacten blijven beschikbaar waar je ook werkt.

## Beheer vanuit één omgeving

Mailboxen, aliassen en wachtwoorden beheer je vanuit het eigen Qevanto Hostingpaneel. Zo houd je de e-mailomgeving overzichtelijk zonder zelf een mailserver te onderhouden.', 1, NOW()),
('SPF, DKIM en DMARC uitgelegd', 'spf-dkim-en-dmarc-uitgelegd', 'E-mail Hosting', 'MBIV Development', CURDATE(),
'Deze drie domeininstellingen helpen om je e-mail beter te beveiligen en betrouwbare aflevering te ondersteunen.',
'## Bescherming tegen misbruik

SPF, DKIM en DMARC geven ontvangende mailservers informatie over welke systemen namens jouw domein mogen verzenden en hoe berichten gecontroleerd moeten worden.

## Minder kans op spam

Een goed ingestelde e-mailomgeving maakt het makkelijker om legitieme berichten te herkennen. Dat helpt de reputatie van je domein en verkleint de kans dat berichten onnodig in spam belanden.

## Laat de instellingen controleren

De juiste DNS-records hangen af van je domein en mailomgeving. Controleer de instellingen zorgvuldig en pas ze aan wanneer je verzendende diensten veranderen.', 1, NOW()),
('Een veilige mailbox voor je organisatie', 'veilige-mailbox-voor-organisatie', 'E-mail Hosting', 'MBIV Development', CURDATE(),
'Met goed beheer, TLS en filters houd je zakelijke e-mail veilig, betrouwbaar en prettig in gebruik.',
'## Beveiligde verbindingen

TLS versleutelt de verbinding tussen je apparaat en de mailomgeving. Daardoor worden berichten en inloggegevens beter beschermd tijdens het gebruik van je mailbox.

## Filters voor ongewenste berichten

Spam- en virusfilters houden veel ongewenste inhoud tegen voordat die je inbox bereikt. Blijf daarnaast alert op onverwachte links, bijlagen en verzoeken om gegevens.

## Duidelijke gebruikers en aliassen

Maak voor medewerkers en functies passende mailboxen en aliassen aan. Met een vaste structuur blijft het beheer duidelijk wanneer je organisatie groeit.', 1, NOW());
