-- Cloud VPS-artikelen voor de Cloud VPS-pagina.
INSERT IGNORE INTO BlogBerichten (Titel, Slug, Categorie, Auteur, PublicatieDatum, Samenvatting, Inhoud, Zichtbaar, AangemaaktOp) VALUES
('Een Cloud VPS kiezen: let op deze resources', 'cloud-vps-resources-kiezen', 'Cloud VPS', 'MBIV Development', CURDATE(),
'vCPU, geheugen en opslag bepalen samen hoe soepel je Cloud VPS draait.',
'## Begin bij je workload

Kijk eerst welke websites, applicaties en databases op de server komen. Een lichte website vraagt iets anders dan een database, API of platform met veel gelijktijdige bezoekers.

## Kies ruimte om te groeien

Reserveer voldoende geheugen voor je huidige gebruik en houd rekening met groei. Extra CPU, RAM en opslag zijn later eenvoudig uit te breiden wanneer je omgeving groter wordt.

## Houd prestaties inzichtelijk

Controleer regelmatig het gebruik van CPU, geheugen en schijfruimte. Zo zie je op tijd wanneer een upgrade verstandig is en voorkom je dat een piek een probleem wordt.', 1, NOW()),
('Cloud VPS beveiligen: de belangrijkste basis', 'cloud-vps-beveiligen', 'Cloud VPS', 'MBIV Development', CURDATE(),
'Met een paar vaste maatregelen maak je een nieuwe Cloud VPS direct een stuk veiliger.',
'## Gebruik veilige toegang

Werk bij voorkeur met SSH-sleutels, een gewone gebruikersaccount en beperkte sudo-rechten. Schakel wachtwoordtoegang voor root uit nadat je de nieuwe toegang hebt getest.

## Beperk netwerktoegang

Open alleen de poorten die je werkelijk gebruikt. Een firewall, actuele software en sterke wachtwoorden vormen samen de basis van een veilige serveromgeving.

## Plan updates en back-ups

Beveiligingsupdates horen bij dagelijks serverbeheer. Combineer ze met snapshots en eigen back-ups, zodat je bij een fout of incident snel kunt herstellen.', 1, NOW()),
('Wanneer past een Cloud VPS bij je organisatie?', 'wanneer-cloud-vps', 'Cloud VPS', 'MBIV Development', CURDATE(),
'Een Cloud VPS biedt meer vrijheid dan webhosting zonder meteen een eigen fysieke server nodig te maken.',
'## Meer controle over je omgeving

Root-toegang is handig wanneer je specifieke software, serverinstellingen of meerdere applicaties zelf wilt beheren. Je bepaalt zelf hoe de omgeving wordt ingericht en uitgebreid.

## Geschikt voor groeiende projecten

Een VPS past bij websites, webshops, API’s en databases die meer resources nodig hebben dan een standaard hostingpakket biedt. Je schaalt mee wanneer bezoekersaantallen of processen toenemen.

## Kies hoeveel beheer je wilt

Je kunt de server zelf beheren of afspraken maken over ondersteuning en onderhoud. Zo sluit de oplossing aan bij je technische kennis en de manier waarop je organisatie werkt.', 1, NOW());
