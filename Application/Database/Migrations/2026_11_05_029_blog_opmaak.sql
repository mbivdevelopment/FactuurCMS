-- Blogberichten netjes maken: lege regels tussen koppen, alinea's en opsommingen.
UPDATE BlogBerichten SET Inhoud =
'## Waarom deze update belangrijk is

Er is een kritieke beveiligingsupdate voor PHP verschenen. Het lek maakt het in specifieke omstandigheden mogelijk om code op de server uit te voeren zonder inloggegevens. Elke website die op een kwetsbare PHP-versie draait, is daarom een doelwit.

Het risico is niet theoretisch: zodra zo''n lek bekend is, scannen geautomatiseerde bots binnen enkele uren het internet op kwetsbare servers.

## Wat betekent dit voor jou?

- Websites op onze shared hosting draaien al op de gepatchte versie — daar hoef je niets voor te doen.
- Heb je een Cloud VPS of eigen server? Controleer dan welke PHP-versie je draait en update zo snel mogelijk.
- Oudere PHP-versies die geen beveiligingsupdates meer krijgen, moet je vervangen door een ondersteunde versie.

## Zo controleer je je PHP-versie

Log in op je server of hostingpaneel en zoek de PHP-versie bij je website-instellingen. Op de command line kan dat met php -v. Vergelijk de versie met de lijst van actief ondersteunde versies op php.net.

## Meer hulp nodig?

Draait jouw site op een versie die geen updates meer krijgt, of weet je niet zeker of je kwetsbaar bent? Open dan een ticket via het klantportaal — wij kijken het direct voor je na en voeren de update zo nodig voor je uit.',
BijgewerktOp = NOW() WHERE Slug = 'kritieke-php-update';

UPDATE BlogBerichten SET Inhoud =
'## Waarom updaten?

Verouderde WordPress-installaties, thema''s en plugins zijn de grootste oorzaak van gehackte websites. Updates dichten beveiligingslekken en houden je site snel en compatibel.

## Voor je begint

- Maak eerst een volledige back-up van je bestanden én database. Bij onze hostingpakketten draait er dagelijks een automatische back-up, maar een eigen momentopname is altijd slim.
- Controleer of je thema en plugins de nieuwe WordPress-versie ondersteunen.
- Schakel caching-plugins tijdelijk uit.

## De juiste volgorde

- Werk eerst je plugins bij.
- Daarna je thema.
- Pas als laatste WordPress zelf, via Dashboard → Updates.
- Test daarna je site: homepagina, contactformulier, winkelmandje en inloggen.

## Automatisch updaten

Kleine beveiligingsupdates installeert WordPress vanzelf. Voor grote versies raden we aan ze handmatig te doen, zodat je meteen ziet als iets breekt.

## Gaat er toch iets mis?

Zet dan je back-up terug en probeer de update opnieuw, plugin voor plugin, om te vinden welke het probleem veroorzaakt. Lukt het niet? Onze support helpt je — open een ticket in het klantportaal.',
BijgewerktOp = NOW() WHERE Slug = 'wordpress-updaten';
