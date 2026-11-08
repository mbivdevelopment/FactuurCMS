<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

/** Vaste informatieblokken (voordelen, stappen, FAQ) per dienstsoort voor de publieke website. */
final class PageContent
{
    /** @return list<array{0:string,1:string,2:string}> icoon, titel, tekst */
    public static function benefits(?string $soort, string $title = ''): array
    {
        $map = [
            'Webhosting' => [
                ['zap', 'NVMe-snelheid', 'Snelle NVMe-opslag met serverside caching.'],
                ['shield', 'Dagelijkse back-ups', 'Nachtelijke back-ups op aparte locatie.'],
                ['lock', 'Gratis SSL', 'Automatisch SSL op ieder domein.'],
                ['layout', 'Eén-klik installaties', 'Installeer WordPress en webshops direct.'],
                ['mail', 'E-mail inbegrepen', 'Mailboxen, webmail en spamfilter inbegrepen.'],
                ['refresh', 'Gratis verhuizing', 'Gratis verhuizing zonder downtime.'],
            ],
            'Reseller Hosting' => [
                ['users', 'Eigen klantenaccounts', 'Beheer accounts voor al je klanten.'],
                ['palette', 'White-label', 'Jouw merk, nameservers en huisstijl.'],
                ['zap', 'Dezelfde snelle servers', 'Snelle hosting met NVMe-opslag en caching.'],
                ['shield', 'Back-ups per account', 'Aparte back-ups voor ieder klantaccount.'],
                ['headset', 'Technische ondersteuning', 'Wij helpen jou met technische vragen.'],
                ['euro', 'Eén vast maandbedrag', 'Eén vast tarief zonder verrassingen.'],
            ],
            'E-mail Hosting' => [
                ['mail', 'Zakelijke mailboxen', 'Professioneel e-mailen op je eigen domeinnaam, met ruime opslag per mailbox.'],
                ['shield', 'Spam- en virusfilter', 'Meerlaagse filtering houdt ongewenste mail buiten de deur.'],
                ['refresh', 'Synchronisatie op elk apparaat', 'IMAP, ActiveSync en webmail: mail, agenda en contacten overal gelijk.'],
                ['lock', 'Versleutelde verbindingen', 'TLS op alle protocollen en SPF, DKIM en DMARC standaard ingesteld.'],
                ['box', 'Archivering en back-up', 'Dagelijkse back-ups van alle mailboxen, zodat niets verloren gaat.'],
                ['headset', 'Hulp bij instellen', 'Wij helpen je met Outlook, Apple Mail of je telefoon.'],
            ],
            'Cloud VPS' => [
                ['cpu', 'Dedicated resources', 'Gegarandeerde vCPU\'s en RAM: geen overboeking, geen buren die je vertragen.'],
                ['zap', 'NVMe-opslag', 'Razendsnelle schijven met hoge IOPS voor databases en applicaties.'],
                ['refresh', 'Direct opschalen', 'Meer CPU, geheugen of schijfruimte nodig? Binnen minuten geregeld, zonder migratie.'],
                ['shield', 'Snapshots en back-ups', 'Maak snapshots voor elke wijziging en herstel in één klik.'],
                ['server', 'Root-toegang', 'Volledige controle met het besturingssysteem van jouw keuze.'],
                ['globe', 'Nederlandse datacenters', 'Lage latency en je data binnen de EU, ISO 27001-gecertificeerd.'],
            ],
            'Dedicated Server' => [
                ['cpu', 'Volledige hardware voor jou', 'Geen virtualisatie: alle rekenkracht en I/O is exclusief van jou.'],
                ['server', 'Enterprise-hardware', 'Server-grade processors, ECC-geheugen en RAID-opslag.'],
                ['globe', 'Redundant netwerk', 'Dubbel uitgevoerde uplinks met uitstekende connectiviteit naar Europa.'],
                ['shield', 'DDoS-bescherming', 'Standaard netwerkbescherming tegen aanvallen.'],
                ['refresh', 'Remote beheer', 'IPMI/KVM-toegang en herinstallatie vanuit het paneel.'],
                ['headset', 'Beheer op maat', 'Wij houden je server up-to-date en gemonitord als je dat wilt.'],
            ],
            'Big Storage' => [
                ['box', 'Terabytes aan ruimte', 'Grote opslagvolumes voor back-ups, media en archieven.'],
                ['shield', 'Redundante opslag', 'Meervoudig opgeslagen data, bestand tegen uitval van schijven.'],
                ['refresh', 'Flexibel uitbreiden', 'Vergroot je volume zonder onderbreking.'],
                ['lock', 'Versleuteld', 'Data in rust en onderweg versleuteld.'],
                ['globe', 'Snelle koppeling', 'Koppel het volume aan je VPS of gebruik S3-compatibele toegang.'],
                ['euro', 'Lage prijs per GB', 'Betaal alleen voor wat je nodig hebt.'],
            ],
            'Domeinnamen' => [
                ['globe', '400+ extensies', 'Van .nl en .com tot .shop en .cloud, in één bestelling.'],
                ['server', 'Gratis DNS-beheer', 'Alle recordtypes zelf beheren, direct actief.'],
                ['lock', 'Gratis SSL', 'Let\'s Encrypt-certificaat inbegrepen bij hosting.'],
                ['refresh', 'Automatische verlenging', 'Wij verlengen tijdig; nooit meer een verlopen domein.'],
                ['shield', 'Transferlock en privacy', 'Beveiligd tegen ongewenste verhuizing en WHOIS-spam.'],
                ['mail', 'Doorsturen inbegrepen', 'Web- en e-mailforwarding zonder extra kosten.'],
            ],
            'SMTP Relay' => [
                ['send', 'Hoge inbox-komst', 'Schone IP-reputatie en correcte authenticatie: je mail komt aan.'],
                ['shield', 'SPF, DKIM en DMARC', 'Wij zetten de records klaar; jij plakt ze in je DNS.'],
                ['zap', 'Direct actief', 'Na betaling staan je SMTP-gegevens in het klantportaal.'],
                ['layout', 'Logboek en statistieken', 'Zie per bericht of het bezorgd, gebounced of uitgesteld is.'],
                ['lock', 'Versleuteld verzenden', 'STARTTLS op poort 587 of SSL op 465, altijd beveiligd.'],
                ['refresh', 'Schaalt mee', 'Meer verzenden? Verhoog je limiet zonder nieuwe inrichting.'],
            ],
            'SSL Certificaten' => [
                ['lock', 'Versleutelde verbinding', 'Bescherm gegevens van bezoekers en klanten met sterke encryptie.'],
                ['shield', 'Vertrouwen en bedrijfsvalidatie', 'OV- en EV-certificaten tonen wie er achter de website zit.'],
                ['globe', 'Wildcard en multi-domain', 'Eén certificaat voor alle subdomeinen of meerdere domeinen.'],
                ['trend', 'Beter in Google', 'HTTPS is een rankingfactor en voorkomt waarschuwingen in de browser.'],
                ['refresh', 'Installatie en verlenging', 'Wij installeren het certificaat en herinneren je tijdig aan verlenging.'],
                ['headset', 'Hulp bij validatie', 'We begeleiden je door de validatie, ook bij EV.'],
            ],
            'VPN' => [
                ['shield', 'Veilig thuiswerken', 'Versleutelde toegang tot het bedrijfsnetwerk vanaf elke locatie.'],
                ['lock', 'Sterke authenticatie', 'Certificaten en twee-factor-authenticatie per gebruiker.'],
                ['users', 'Per medewerker beheerd', 'Gebruikers toevoegen en intrekken in één klik.'],
                ['globe', 'Site-to-site', 'Verbind kantoren en datacenters veilig met elkaar.'],
                ['trend', 'Inzicht en logging', 'Zie wie wanneer verbonden was, voor audits en beveiliging.'],
                ['headset', 'Installatie op werkplekken', 'Wij richten de VPN in op laptops, telefoons en routers.'],
            ],
            'VoIP' => [
                ['phone', 'Bellen via internet', 'Vaste nummers, mobiel bereikbaar — zonder ISDN of dure lijnen.'],
                ['users', 'Meegroeiende centrale', 'Voeg toestellen en medewerkers toe wanneer je wilt.'],
                ['refresh', 'Nummerbehoud', 'Behoud je bestaande nummers bij de overstap.'],
                ['chat', 'Keuzemenu en wachtrij', 'Professionele ontvangst met IVR, voicemail-naar-mail en doorschakelen.'],
                ['layout', 'Softphone en app', 'Bellen vanaf laptop of smartphone met je zakelijke nummer.'],
                ['euro', 'Lage gesprekskosten', 'Transparante tarieven en onbeperkt onderling bellen.'],
            ],
            'Licenties' => [
                ['tag', 'Microsoft 365', 'Office-apps, Exchange-mail en Teams per gebruiker per maand.'],
                ['file', 'Altijd legaal en up-to-date', 'Licenties op naam, met de nieuwste versies.'],
                ['refresh', 'Maandelijks op- en afschalen', 'Betaal alleen voor het aantal gebruikers van dat moment.'],
                ['shield', 'Beveiliging inbegrepen', 'Defender, MFA en beleid centraal ingericht.'],
                ['box', 'Overige software', 'Ook antivirus, back-upsoftware en serverlicenties.'],
                ['headset', 'Beheer door ons', 'Wij regelen de licenties, jij werkt gewoon door.'],
            ],
            'Werkplek Beheer' => [
                ['headset', 'Eén aanspreekpunt', 'Voor alle vragen over laptops, software en accounts.'],
                ['shield', 'Beveiligd en gepatcht', 'Automatische updates, antivirus en schijfversleuteling op elke werkplek.'],
                ['refresh', 'Proactief onderhoud', 'Wij signaleren problemen vóór je medewerkers ze merken.'],
                ['users', 'On- en offboarding', 'Nieuwe medewerker? Account, laptop en toegang staan klaar.'],
                ['box', 'Back-up van werkplekken', 'Documenten en instellingen veilig in de cloud.'],
                ['euro', 'Vast bedrag per werkplek', 'Voorspelbare kosten, geen verrassingen.'],
            ],
            'Informatieve Websites' => [
                ['layout', 'Ontwerp op maat', 'Geen template, maar een website die bij je merk past.'],
                ['zap', 'Snel en toegankelijk', 'Geoptimaliseerd voor Core Web Vitals en toegankelijkheid.'],
                ['trend', 'SEO-fundament', 'Technisch in orde voor Google vanaf de lancering.'],
                ['edit', 'Zelf te beheren', 'Teksten en afbeeldingen wijzig je eenvoudig zelf.'],
                ['lock', 'Veilig gehost', 'Draait op onze eigen hosting met SSL en back-ups.'],
                ['headset', 'Onderhoud en support', 'Updates en hulp na oplevering, zonder gedoe.'],
            ],
            'Webshops' => [
                ['cart', 'Verkoop zonder limiet', 'WooCommerce, Magento of maatwerk — afgestemd op je assortiment.'],
                ['euro', 'iDEAL en meer', 'Alle gangbare betaalmethoden via Mollie, Pay. of Adyen.'],
                ['box', 'Koppeling met voorraad', 'Verbinding met je kassa, boekhouding of magazijn.'],
                ['zap', 'Snelle checkout', 'Minder afhakers door een geoptimaliseerd bestelproces.'],
                ['trend', 'Groei-gericht', 'Conversie-optimalisatie en analytics vanaf dag één.'],
                ['shield', 'Veilig en PCI-proof', 'Betaalgegevens blijven bij de betaalprovider.'],
            ],
            'API Koppelingen' => [
                ['code', 'Systemen die samenwerken', 'Alle systemen werken automatisch samen.'],
                ['refresh', 'Realtime synchronisatie', 'Gegevens blijven altijd actueel.'],
                ['shield', 'Veilig en gelogd', 'Veilige koppelingen met duidelijke logging.'],
                ['box', 'Standaard én maatwerk', 'Koppelingen voor bekende en eigen systemen.'],
                ['trend', 'Schaalbaar', 'Groeit mee met jouw volumes.'],
                ['headset', 'Beheer en onderhoud', 'Wij onderhouden koppelingen bij wijzigingen.'],
            ],
            'Marketing SEO' => [
                ['trend', 'Hoger in Google', 'Technische en inhoudelijke optimalisatie voor duurzame posities.'],
                ['search', 'Zoekwoordonderzoek', 'Wij vinden de termen waar jouw klanten echt op zoeken.'],
                ['layout', 'Contentstrategie', 'Teksten die scoren én overtuigen.'],
                ['link', 'Autoriteit opbouwen', 'Kwalitatieve backlinks en vermeldingen.'],
                ['file', 'Maandelijkse rapportage', 'Heldere cijfers over posities, bezoekers en conversie.'],
                ['headset', 'Vaste specialist', 'Een aanspreekpunt dat jouw markt kent.'],
            ],
            'Social Media Onderhoud' => [
                ['chat', 'Consistente aanwezigheid', 'Regelmatig posten op de kanalen die voor jou tellen.'],
                ['image', 'Content op maat', 'Visuals en teksten in jouw huisstijl.'],
                ['calendar', 'Contentkalender', 'Vooruit gepland, door jou goedgekeurd.'],
                ['users', 'Community-beheer', 'Reacties en berichten snel en netjes beantwoord.'],
                ['trend', 'Advertenties', 'Gerichte campagnes op Facebook, Instagram en LinkedIn.'],
                ['file', 'Rapportage', 'Maandelijks inzicht in bereik en groei.'],
            ],
            'Linkbuilding' => [
                ['link', 'Relevante backlinks', 'Links van websites die bij jouw branche passen.'],
                ['shield', 'Veilig en duurzaam', 'Geen linkfarms: alleen witte-hoed-methodes.'],
                ['trend', 'Meetbare groei', 'Autoriteit en posities die aantoonbaar stijgen.'],
                ['edit', 'Gastblogs en PR', 'Inhoudelijke artikelen op externe platforms.'],
                ['file', 'Transparante rapportage', 'Je ziet precies welke links we hebben geplaatst.'],
                ['headset', 'Strategie op maat', 'Afgestemd op je concurrentie en doelen.'],
            ],
        ];
        if (isset($map[(string) $soort])) {
            return $map[(string) $soort];
        }
        $t = $title !== '' ? mb_strtolower($title) : 'deze dienst';

        return [
            ['headset', 'Persoonlijk advies', 'Een specialist denkt met je mee over ' . $t . ' en de beste aanpak.'],
            ['shield', 'Betrouwbaar en veilig', 'Eigen infrastructuur, monitoring en back-ups in Nederland.'],
            ['zap', 'Snel geregeld', 'Na akkoord gaan we direct aan de slag; je weet precies waar je staat.'],
            ['euro', 'Heldere prijzen', 'Vooraf duidelijkheid over kosten, zonder verrassingen achteraf.'],
            ['refresh', 'Flexibel op- en afschalen', 'Groei je? Dan groeit de oplossing mee.'],
            ['users', 'Eén aanspreekpunt', 'Korte lijnen met mensen die jouw situatie kennen.'],
        ];
    }

    /** @return list<array{0:string,1:string}> titel, tekst */
    public static function steps(?string $soort, bool $orderable): array
    {
        $byKind = [
            'Domeinnamen' => [['Zoek je domeinnaam', 'Typ de naam en zie direct welke extensies vrij zijn of verhuisd kunnen worden.'], ['Kies extensies', 'Voeg één of meerdere extensies toe aan je bestelling.'], ['Betaal veilig online', 'Met iDEAL of creditcard; registratie start automatisch.'], ['Direct actief', 'Binnen minuten staat je domein in je klantportaal, met DNS-beheer.']],
            'SMTP Relay' => [['Kies je pakket', 'Bepaal hoeveel e-mails je per maand verstuurt; upgraden kan altijd.'], ['Bestel online', 'Vul je domeinnaam in en betaal veilig met iDEAL of creditcard.'], ['Zet de DNS-records', 'Plak de SPF-, DKIM- en DMARC-records die wij voor je klaarzetten.'], ['Verbind je applicatie', 'Vul host, poort en inloggegevens in bij je website of app en verstuur.']],
            'Cloud VPS' => [['Kies je configuratie', 'Selecteer vCPU, geheugen, opslag en besturingssysteem.'], ['Bestel en betaal', 'Veilig online, zonder langlopend contract.'], ['Automatische uitrol', 'Je server wordt binnen minuten uitgerold en ingericht.'], ['Beheer in je portaal', 'Herstarten, snapshots en upgrades regel je zelf.']],
        ];
        if (isset($byKind[(string) $soort])) {
            return $byKind[(string) $soort];
        }

        return $orderable
            ? [['Kies je pakket', 'Vergelijk de pakketten en kies wat bij je past; upgraden kan altijd.'], ['Bestel online', 'Vul je gegevens in en betaal veilig met iDEAL of creditcard.'], ['Wij richten het in', 'Na betaling wordt je dienst automatisch geactiveerd en ingericht.'], ['Aan de slag', 'Inloggegevens staan in je klantportaal; onze support staat voor je klaar.']]
            : [['Kennismaking', 'We bespreken je situatie, wensen en doelen — vrijblijvend.'], ['Voorstel op maat', 'Je ontvangt binnen één werkdag een helder plan met vaste prijs.'], ['Uitvoering', 'Wij realiseren de oplossing en houden je op de hoogte.'], ['Nazorg en beheer', 'Na oplevering blijven we beschikbaar voor beheer en vragen.']];
    }

    /** @return list<array{0:string,1:string}> vraag, antwoord */
    public static function faq(?string $soort, string $title, bool $orderable): array
    {
        $byKind = [
            'Webhosting' => [
                ['Kan ik mijn bestaande website verhuizen?', 'Ja, wij verhuizen je website en e-mail kosteloos en zonder downtime. Na je bestelling nemen we contact op om de verhuizing in te plannen.'],
                ['Welk controlepaneel gebruiken jullie?', 'Je beheert bestanden, databases, e-mail en installaties in het eigen Qevanto Hostingpaneel.'],
                ['Kan ik later upgraden?', 'Altijd. Upgraden naar een groter pakket gebeurt direct, zonder verhuizing en zonder downtime.'],
                ['Hoe zit het met back-ups?', 'Elke nacht maken we een back-up die we 14 dagen bewaren op een aparte locatie. Terugzetten doe je zelf in het paneel of via onze support.'],
                ['Wat is de opzegtermijn?', 'Pakketten zijn maandelijks of jaarlijks en opzegbaar tegen het einde van de looptijd, zonder extra kosten.'],
            ],
            'Cloud VPS' => [
                ['Wat is het verschil met webhosting?', 'Een VPS geeft je een eigen server met root-toegang en gegarandeerde resources. Ideaal voor applicaties, meerdere sites of specifieke software.'],
                ['Kan ik mijn server zelf beheren?', 'Ja, je krijgt volledige root-toegang. Wil je liever dat wij updates en beveiliging doen? Kies dan voor beheerde VPS.'],
                ['Hoe snel is mijn VPS beschikbaar?', 'Na betaling wordt je server automatisch uitgerold; meestal binnen 5 minuten.'],
                ['Kan ik opschalen?', 'CPU, geheugen en schijfruimte vergroot je direct vanuit je klantportaal, zonder herinstallatie.'],
                ['Welke besturingssystemen zijn er?', 'Ubuntu, Debian, AlmaLinux, Rocky Linux en Windows Server (met licentie).'],
            ],
            'Domeinnamen' => [
                ['Hoe lang duurt een registratie?', 'De meeste extensies zijn binnen enkele minuten actief na betaling.'],
                ['Kan ik mijn domein verhuizen?', 'Ja. Vraag bij je huidige provider de verhuiscode (auth-code) op en voer die in tijdens het bestellen.'],
                ['Is DNS-beheer inbegrepen?', 'Ja, je beheert alle DNS-records gratis vanuit je klantportaal.'],
                ['Wordt mijn domein automatisch verlengd?', 'Standaard wel, zodat je domein nooit verloopt. Opzeggen kan tot 30 dagen voor de vervaldatum.'],
            ],
        ];
        if (isset($byKind[(string) $soort])) {
            return $byKind[(string) $soort];
        }
        $t = mb_strtolower($title !== '' ? $title : 'deze dienst');

        return $orderable
            ? [
                ['Hoe snel is ' . $t . ' actief?', 'Na online betaling activeren we de dienst automatisch; in de meeste gevallen binnen enkele minuten. Je ontvangt alle gegevens per e-mail en in je klantportaal.'],
                ['Kan ik tussentijds wijzigen of upgraden?', 'Ja, upgraden kan op elk moment. Het verschil wordt naar rato verrekend op je volgende factuur.'],
                ['Wat zijn de contractvoorwaarden?', 'Je kiest zelf de looptijd. Diensten worden automatisch verlengd en zijn opzegbaar tegen het einde van de periode.'],
                ['Hoe werkt de facturatie?', 'Je ontvangt facturen per e-mail en in je klantportaal. Betalen kan met iDEAL, creditcard of automatische incasso.'],
                ['Krijg ik hulp bij de overstap?', 'Zeker. Onze specialisten helpen bij migratie en instellingen, zonder extra kosten.'],
            ]
            : [
                ['Hoe start een traject voor ' . $t . '?', 'Met een vrijblijvend gesprek. Daarna ontvang je binnen één werkdag een voorstel met vaste prijs en planning.'],
                ['Wat kost ' . $t . '?', 'Dat hangt af van je wensen. Je krijgt vooraf een heldere offerte; wij werken niet met verborgen kosten.'],
                ['Werken jullie ook voor kleine bedrijven?', 'Ja. Van zzp\'er tot mkb-organisatie: we schalen de oplossing naar je situatie.'],
                ['Blijven jullie na oplevering betrokken?', 'Altijd. Beheer, onderhoud en support regelen we in een passend serviceniveau.'],
                ['Zit ik vast aan een lang contract?', 'Nee. Beheer en onderhoud zijn maandelijks opzegbaar, tenzij we anders afspreken.'],
            ];
    }

    /** Vertrouwenscijfers. @return list<array{0:string,1:string,2:string}> waarde, label, icoon */
    public static function trust(int $datacenters = 0): array
    {
        return [
            ['99,9%', 'Uptime-garantie', 'zap'],
            ['24/7', 'Monitoring & support', 'headset'],
            [$datacenters > 0 ? (string) $datacenters : 'NL', $datacenters > 0 ? 'Datacenters' : 'Datacenters in Nederland', 'pin'],
            ['ISO 27001', 'Gecertificeerde locaties', 'shield'],
        ];
    }

    /** Groepsnaam uit Product::SOORTEN voor een dienstsoort. */
    public static function groupOf(?string $soort): string
    {
        foreach (Product::SOORTEN as $group => $kinds) {
            if (isset($kinds[(string) $soort])) {
                return $group;
            }
        }

        return '';
    }
}
