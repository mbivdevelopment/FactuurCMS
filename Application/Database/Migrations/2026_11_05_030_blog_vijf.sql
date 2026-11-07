-- Vijf extra blogberichten voor de blogcarrousel.
INSERT IGNORE INTO BlogBerichten (Titel, Slug, Categorie, Auteur, PublicatieDatum, Samenvatting, Inhoud, Zichtbaar, AangemaaktOp) VALUES
('Je domein verhuizen zonder downtime', 'domein-verhuizen-zonder-downtime', 'Handleidingen', 'MBIV Development', CURDATE(),
'Een domein verhuizen hoeft geen downtime te betekenen. Met de juiste volgorde — DNS eerst, verhuiscode daarna — blijft je site gewoon bereikbaar.',
'## Waarom verhuizen misgaat

De meeste downtime bij een domeinverhuizing komt niet door de verhuizing zelf, maar door DNS-wijzigingen die te vroeg of te laat worden doorgevoerd. De juiste volgorde voorkomt vrijwel alle problemen.

## Stap voor stap

- Zet je DNS alvast over naar de nieuwe nameservers, of kopieer je DNS-records naar de nieuwe provider voordat je de verhuizing start.
- Vraag de verhuiscode (EPP/authcode) op bij je huidige registrar.
- Start de verhuizing bij de nieuwe registrar en bevestig de e-mail van de oude registrar direct.
- Laat de TTL van je DNS-records een dag voor de verhuizing verlagen naar 300 seconden.

## DNS en TTL zijn de sleutel

DNS-caching bij providers bepaalt hoe snel wijzigingen zichtbaar zijn. Met een lage TTL schakelt het verkeer binnen enkele minuten om in plaats van uren. Vergeet na de verhuizing niet de TTL weer te verhogen.

## E-mail meeverhuizen

Verhuist je domein met mailboxen? Zet dan eerst de MX-records goed bij de nieuwe provider voordat de verhuizing afrondt, anders kan e-mail tijdelijk binnenlopen bij de oude provider.', 1, NOW()),
('SSL-certificaten: van self-signed tot wildcard', 'ssl-certificaten-uitleg', 'Handleidingen', 'MBIV Development', CURDATE(),
'Welk SSL-certificaat heb je nodig? Een overzicht van domeinvalidatie, wildcard en extended validation — en wanneer welke keuze logisch is.',
'## Waarom SSL verplicht is

Browsers markeren sites zonder HTTPS als onveilig, en zoekmachines ranken ze lager. Gelukkig is een certificaat tegenwoordig gratis en automatisch te vernieuwen.

## De drie smaken

- Domeinvalidatie (DV): bewijst dat je het domein beheert. Gratis via Let''s Encrypt, uitgegeven in seconden, en voor bijna elke site de juiste keuze.
- Wildcard: dekt alle subdomeinen (*.voorbeeld.nl). Handig als je veel subdomeinen draait, zoals klantomgevingen of staging.
- Extended Validation (EV): extra identiteitscontrole van je bedrijf. Duurder en voor de meeste sites onnodig.

## Automatisch vernieuwen

Certificaten lopen af. Kies daarom altijd voor automatische vernieuwing — al onze hostingpakketten vernieuwen certificaten vanzelf, zodat je site nooit zonder HTTPS komt te staan.

## Mixed content

Na het aanzetten van HTTPS kunnen pagina''s nog onveilige bronnen laden (afbeeldingen of scripts via http://). Controleer de browserconsole en pas alle verwijzingen aan naar https://.', 1, NOW()),
('Dagelijkse back-ups: hoe wij je data beschermen', 'dagelijkse-backups', 'Product', 'MBIV Development', CURDATE(),
'Een back-up die je niet kunt terugzetten is geen back-up. Zo is ons back-upbeleid opgebouwd — en hoe je zelf een restore aanvraagt.',
'## Waarom wij dagelijks back-uppen

Hardware faalt, updates gaan mis en mensen maken fouten. Zonder recente back-up kan één incident betekenen dat je site of e-mail voor goed weg is. Daarom draait bij al onze hostingpakketten dagelijks een automatische back-up.

## Hoe het werkt

- Elke nacht maken we een volledige snapshot van je bestanden en databases.
- Back-ups worden versleuteld opgeslagen op een aparte infrastructuur, buiten hetzelfde datacenter.
- We bewaren meerdere dagen aan restore points, zodat je ook kunt terugkeren naar een versie van vorige week.

## Zelf een restore aanvragen

Open een ticket in het klantportaal met de gewenste datum en wat er teruggezet moet worden — een bestand, een database of de hele site. Kleine restores staan er vaak binnen het uur.

## Test je restores

Een back-up is pas bewezen als je hem hebt teruggezet. Wij draaien periodiek restore-tests; voor je eigen gevoel van zekerheid kun je altijd een test-restore aanvragen.', 1, NOW()),
('Wat is SMTP relay en wanneer heb je het nodig?', 'wat-is-smtp-relay', 'Handleidingen', 'MBIV Development', CURDATE(),
'Contactformulieren, orderbevestigingen en wachtwoordresets moeten áltijd aankomen. SMTP relay maakt het verschil tussen inbox en spamfolder.',
'## Het probleem met mail vanaf je website

Standaard verstuurt een website e-mail vanaf de webserver zelf. Die e-mail komt steeds vaker in de spamfolder terecht — of komt helemaal niet aan — omdat de server geen aantoonbare reputatie heeft.

## Wat doet SMTP relay?

Met SMTP relay verstuur je mail via een dedicated mailinfrastructuur met een opgebouwde reputatie, in plaats van via de webserver. SPF, DKIM en DMARC zijn goed ingesteld, zodat ontvangers de mail vertrouwen.

## Wanneer heb je het nodig?

- Transactionele e-mail: orderbevestigingen, wachtwoordresets, facturen.
- Contactformulieren waarvan elke inzending moet aankomen.
- Nieuwsbrieven vanuit je eigen applicatie.

## Aan de slag

SMTP Relay is bij ons als losse dienst beschikbaar. Je krijgt verzendgegevens (host, poort, gebruikersnaam en wachtwoord) die je in vrijwel elke applicatie of plugin kunt invullen, plus duidelijke DNS-instructies voor SPF en DKIM.', 1, NOW()),
('Waarom je website traag is (en wat je eraan doet)', 'website-traag-oorzaken', 'Handleidingen', 'MBIV Development', CURDATE(),
'Een trage site kost bezoekers én conversie. Dit zijn de vijf meest voorkomende oorzaken die we tegenkomen — en de snelste fix voor elke.',
'## Meet eerst

Voordat je iets aanpast: meet. Tools als PageSpeed Insights en WebPageTest laten precies zien waar de tijd in zit — serverreactie, afbeeldingen, of render-blokkerende scripts.

## De vijf gebruikelijke verdachten

- Ongeschaalde afbeeldingen: een 4 MB foto op een mobiele pagina. Comprimeer en gebruik moderne formaten zoals WebP.
- Te veel plugins: elke plugin voegt code en database-queries toe. Verwijder wat je niet gebruikt.
- Geen caching: zonder cache bouwt de server elke pagina opnieuw. Schakel server- of plugin-caching in.
- Trage queries: een ongeoptimaliseerde database is vaak de boosdoener bij webshops.
- Verre hosting: een server aan de andere kant van de wereld kost honderden milliseconden per request.

## Wanneer hosting de bottleneck is

Draait je site op een gedeeld pakket met te weinig geheugen of CPU voor je verkeer? Opschalen lost meer op dan nog een optimalisatie-plugin. Bekijk onze hostingpakketten of neem contact op voor advies.', 1, NOW());
