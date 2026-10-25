<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Definities van de website-modules die via één generiek beheerscherm (/beheer/website/{type}) worden beheerd.
 * Veldtypes: text, textarea, select, date, number, image, bool, icon.
 */
final class Content
{
    public const BUDGETS = ['' => 'Geen indicatie', 'Tot € 1.000' => 'Tot € 1.000', '€ 1.000 – € 2.500' => '€ 1.000 – € 2.500', '€ 2.500 – € 5.000' => '€ 2.500 – € 5.000', '€ 5.000 – € 10.000' => '€ 5.000 – € 10.000', 'Vanaf € 10.000' => 'Vanaf € 10.000'];

    public const ICONS = ['globe', 'server', 'cloud', 'cpu', 'mail', 'layout', 'cart', 'lock', 'shield', 'phone', 'trend', 'link', 'code', 'refresh', 'zap', 'box', 'headset', 'target', 'star', 'users', 'file', 'chat'];

    /** @return array<string, array<string, mixed>> */
    public static function types(): array
    {
        return [
            'paginas' => [
                'table' => 'Paginas', 'label' => "Pagina's", 'single' => 'pagina', 'icon' => 'layout', 'title' => 'Titel',
                'desc' => 'Pagina\'s in het megamenu (Cloudoplossingen en ICT & Development)', 'order' => "FIELD(Categorie, 'cloud', 'ict', 'overig'), Groep, Volgorde, Id", 'public' => '/oplossingen/',
                'fields' => [
                    'Titel' => ['Titel', 'tag', 'text', true], 'Slug' => ['URL-naam (leeg = automatisch)', 'link', 'text'],
                    'Categorie' => ['Menu', 'layout', 'select', true, ['cloud' => 'Cloudoplossingen', 'ict' => 'ICT & Development', 'overig' => 'Niet in menu']],
                    'Groep' => ['Kolom in megamenu (bv. Domeinen, Hosting, Servers)', 'layout', 'text'],
                    'Icoon' => ['Icoon', 'star', 'icon'], 'ProductGroep' => ['Pakketten tonen uit productgroep', 'box', 'productgroup'],
                    'Samenvatting' => ['Korte omschrijving (menu en kaarten)', 'note', 'text'],
                    'Inhoud' => ['Inhoud (lege regel = alinea, "## " = kop, "- " = opsomming)', 'note', 'textarea'],
                    'Afbeelding' => ['Afbeelding', 'image', 'image'], 'Volgorde' => ['Volgorde', 'hash', 'number'], 'Zichtbaar' => ['Zichtbaar op de website', 'eye', 'bool'],
                ],
                'columns' => ['Titel' => 'title', 'Categorie' => 'badge', 'Groep' => 'text', 'Volgorde' => 'text', 'Zichtbaar' => 'bool'],
            ],
            'blog' => [
                'table' => 'BlogBerichten', 'label' => 'Blog', 'single' => 'blogbericht', 'icon' => 'note', 'title' => 'Titel',
                'desc' => 'Berichten in de blogcarrousel op de homepagina', 'order' => 'PublicatieDatum DESC, Id DESC', 'public' => '/blog/',
                'fields' => [
                    'Titel' => ['Titel', 'tag', 'text', true], 'Slug' => ['URL-naam (leeg = automatisch)', 'link', 'text'],
                    'Categorie' => ['Categorie', 'layout', 'text', true], 'Auteur' => ['Auteur', 'user', 'text'],
                    'PublicatieDatum' => ['Publicatiedatum', 'calendar', 'date', true],
                    'Samenvatting' => ['Samenvatting', 'note', 'textarea'], 'Inhoud' => ['Inhoud (lege regel = alinea, "## " = kop, "- " = opsomming)', 'note', 'textarea'],
                    'Afbeelding' => ['Afbeelding', 'image', 'image'], 'Zichtbaar' => ['Gepubliceerd', 'eye', 'bool'],
                ],
                'columns' => ['Afbeelding' => 'image', 'Titel' => 'title', 'Categorie' => 'badge', 'PublicatieDatum' => 'date', 'Zichtbaar' => 'bool'],
            ],
            'portfolio' => [
                'table' => 'PortfolioItems', 'label' => 'Portfolio', 'single' => 'portfolio-item', 'icon' => 'image', 'title' => 'Titel', 'slug' => true,
                'desc' => 'Projecten in de portfoliocarrousel en op /portfolio', 'order' => 'Volgorde, Id DESC', 'public' => '/portfolio/',
                'links' => [['/beheer/website/portfoliocategorieen', 'layout', 'Categorieën beheren']],
                'fields' => [
                    'Titel' => ['Titel', 'tag', 'text', true], 'Klant' => ['Klant', 'building', 'text'],
                    'Categorie' => ['Categorie', 'layout', 'lookup', true, 'PortfolioCategorieen'],
                    'Budget' => ['Indicatie budget', 'euro', 'select', false, self::BUDGETS],
                    'Url' => ['Link naar project', 'link', 'url'], 'Volgorde' => ['Volgorde', 'hash', 'number'],
                    'Inhoud' => ['Projectverhaal (eerste alinea = korte tekst op de kaart)', 'note', 'textarea'],
                    'Afbeelding' => ['Afbeelding', 'image', 'image'], 'Zichtbaar' => ['Zichtbaar op de website', 'eye', 'bool'],
                ],
                'columns' => ['Afbeelding' => 'image', 'Titel' => 'title', 'Klant' => 'text', 'Categorie' => 'badge', 'Budget' => 'text', 'Zichtbaar' => 'bool'],
            ],
            'portfoliocategorieen' => [
                'table' => 'PortfolioCategorieen', 'label' => 'Portfoliocategorieën', 'single' => 'categorie', 'icon' => 'layout', 'title' => 'Naam',
                'desc' => 'Categorieën om uit te kiezen bij een portfolio-item', 'order' => 'Volgorde, Naam', 'public' => null,
                'links' => [['/beheer/website/portfolio', 'image', 'Terug naar portfolio']],
                'fields' => ['Naam' => ['Naam', 'tag', 'text', true], 'Volgorde' => ['Volgorde', 'hash', 'number']],
                'columns' => ['Naam' => 'title', 'Volgorde' => 'text'],
            ],
            'kennisbank' => [
                'table' => 'KennisbankArtikelen', 'label' => 'Kennisbank', 'single' => 'artikel', 'icon' => 'book', 'title' => 'Titel',
                'desc' => 'Hulpartikelen voor klanten; de nieuwste staan op de homepagina', 'order' => 'Categorie, Volgorde, Id', 'public' => '/kennisbank/',
                'fields' => [
                    'Titel' => ['Titel', 'tag', 'text', true], 'Slug' => ['URL-naam (leeg = automatisch)', 'link', 'text'],
                    'Categorie' => ['Categorie (bv. E-mail, Domeinen, Hosting)', 'layout', 'text', true], 'Volgorde' => ['Volgorde', 'hash', 'number'],
                    'Samenvatting' => ['Samenvatting', 'note', 'textarea'], 'Inhoud' => ['Inhoud (lege regel = alinea, "## " = kop, "- " = opsomming)', 'note', 'textarea'],
                    'Zichtbaar' => ['Zichtbaar op de website', 'eye', 'bool'],
                ],
                'columns' => ['Titel' => 'title', 'Categorie' => 'badge', 'Weergaven' => 'text', 'Zichtbaar' => 'bool'],
            ],
            'logos' => [
                'table' => 'Logos', 'label' => "Logo's", 'single' => 'logo', 'icon' => 'star', 'title' => 'Naam',
                'desc' => 'Logocarrousel per groep op de homepagina', 'order' => 'Groep, Volgorde, Id', 'public' => null,
                'fields' => [
                    'Naam' => ['Naam', 'tag', 'text', true], 'Groep' => ['Groep (bv. Webservers, CMS & webshops)', 'layout', 'text', true],
                    'Url' => ['Link (optioneel)', 'link', 'url'], 'Volgorde' => ['Volgorde', 'hash', 'number'],
                    'Afbeelding' => ['Logo (SVG of PNG)', 'image', 'image'], 'Zichtbaar' => ['Zichtbaar op de website', 'eye', 'bool'],
                ],
                'columns' => ['Afbeelding' => 'image', 'Naam' => 'title', 'Groep' => 'badge', 'Volgorde' => 'text', 'Zichtbaar' => 'bool'],
            ],
            'team' => [
                'table' => 'Teamleden', 'label' => 'Team', 'single' => 'teamlid', 'icon' => 'users', 'title' => 'Naam',
                'desc' => 'Teamleden op de pagina Over ons', 'order' => 'Volgorde, Id', 'public' => null,
                'fields' => [
                    'Naam' => ['Naam', 'user', 'text', true], 'Functie' => ['Functie', 'tag', 'text'],
                    'Email' => ['E-mailadres', 'mail', 'email'], 'LinkedIn' => ['LinkedIn-profiel', 'linkedin', 'url'],
                    'Volgorde' => ['Volgorde', 'hash', 'number'], 'Bio' => ['Over deze persoon', 'note', 'textarea'],
                    'Afbeelding' => ['Foto', 'image', 'image'], 'Zichtbaar' => ['Zichtbaar op de website', 'eye', 'bool'],
                ],
                'columns' => ['Afbeelding' => 'image', 'Naam' => 'title', 'Functie' => 'text', 'Volgorde' => 'text', 'Zichtbaar' => 'bool'],
            ],
            'tijdlijn' => [
                'table' => 'Tijdlijn', 'label' => 'Tijdlijn', 'single' => 'moment', 'icon' => 'clock', 'title' => 'Titel',
                'desc' => 'Onze geschiedenis op de pagina Over ons: mijlpalen, lanceringen en overnames', 'order' => 'Volgorde, Jaar, Id', 'public' => null,
                'fields' => [
                    'Jaar' => ['Jaar of periode', 'calendar', 'text', true], 'Titel' => ['Titel', 'tag', 'text', true],
                    'Soort' => ['Soort', 'flag', 'select', true, ['mijlpaal' => 'Mijlpaal', 'lancering' => 'Lancering', 'overname' => 'Overname']],
                    'Volgorde' => ['Volgorde', 'hash', 'number'], 'Verhaal' => ['Kort verhaal', 'note', 'textarea'],
                    'Afbeelding' => ['Afbeelding (optioneel)', 'image', 'image'], 'Zichtbaar' => ['Zichtbaar op de website', 'eye', 'bool'],
                ],
                'columns' => ['Jaar' => 'text', 'Titel' => 'title', 'Soort' => 'badge', 'Volgorde' => 'text', 'Zichtbaar' => 'bool'],
            ],
            'datacenters' => [
                'table' => 'Datacenters', 'label' => 'Datacenters', 'single' => 'datacenter', 'icon' => 'pin', 'title' => 'Naam',
                'desc' => 'Locaties op de wereldkaart van de homepagina', 'order' => 'Volgorde, Id', 'public' => null,
                'fields' => [
                    'Naam' => ['Naam', 'server', 'text', true], 'Stad' => ['Stad', 'map', 'text', true], 'Land' => ['Land', 'globe', 'text', true],
                    'Status' => ['Status', 'zap', 'select', true, ['actief' => 'Actief', 'binnenkort' => 'Binnenkort']],
                    'Latitude' => ['Breedtegraad (bv. 32.7767)', 'pin', 'decimal', true], 'Longitude' => ['Lengtegraad (bv. -96.7970)', 'pin', 'decimal', true],
                    'Volgorde' => ['Volgorde', 'hash', 'number'], 'Omschrijving' => ['Omschrijving', 'note', 'textarea'],
                    'Zichtbaar' => ['Zichtbaar op de website', 'eye', 'bool'],
                ],
                'columns' => ['Naam' => 'title', 'Stad' => 'text', 'Land' => 'text', 'Status' => 'badge', 'Zichtbaar' => 'bool'],
            ],
        ];
    }

    /** @return array<string, mixed>|null */
    public static function type(string $key): ?array
    {
        $types = self::types();

        return isset($types[$key]) ? ['key' => $key] + $types[$key] : null;
    }

    /**
     * Keuzes voor een lookup-veld uit een tabel (kolom Naam); de huidige waarde blijft altijd kiesbaar.
     * @return array<string, string>
     */
    public static function lookup(string $table, string $current = ''): array
    {
        $names = array_column(\App\Core\Database::all('SELECT Naam FROM `' . $table . '` ORDER BY Volgorde, Naam'), 'Naam');
        if ($current !== '' && !in_array($current, $names, true)) {
            $names[] = $current;
        }

        return ['' => '- Kies een categorie -'] + array_combine($names, $names);
    }
}
