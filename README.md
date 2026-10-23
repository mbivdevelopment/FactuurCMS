# FactuurCMS

Hosting-facturatieplatform met eigen PHP MVC (geen framework, geen Composer nodig) en MySQL/MariaDB met PascalCase-tabellen (`Gebruikers`, `Documenten`, `DocumentRegels`, `KlantId`, ...).

- **Beheer** (`/beheer`): dashboard (omzet, MRR, openstaand), klanten (klantkaart met tabs, inloggen als klant), diensten/abonnementen, facturen en offertes (regels, btw, PDF, versturen, betalingen, crediteren, offerte -> factuur), tickets (interne notities), producten, medewerkers, instellingen, updates
- **Klantportaal** (`/klant`): diensten, facturen, offertes (akkoord/afwijzen), PDF-download, tickets, profiel
- **Website** (`/`): diensten/pakketten per groep met bestelflow (maakt dienst + factuur aan), contactformulier
- **Instellingen**: bedrijfsgegevens (KvK, btw, IBAN), branding (logo, licht logo, favicon, kleur), facturatie (prefixen, termijnen, btw, voetteksten), e-mail (log / PHP mail() / SMTP + testmail), website-teksten
- **GitHub-updater**: repo + branch + token, haalt de code op en voert migraties uit. Overschrijft nooit `Application/Config/database.php`, `Application/Config/app.php`, `.htaccess`, `storage/` en geüploade branding

## Installatie (shared hosting / public_html)

1. Upload alle bestanden naar `public_html` (of een submap). Vereist PHP 8.1+ met `pdo_mysql`, `mbstring`, `openssl`, `curl`, `zip`, `gd`.
2. Maak een database aan en importeer via phpMyAdmin `Application/Database/schema.sql` en daarna `Application/Database/seed.sql` (demo-data).
3. Vul `Application/Config/database.php` in met je databasegegevens.
4. Zorg dat `storage/` en `Application/Theme/assets/branding/` schrijfbaar zijn.
5. Cronjob (1x per dag) voor herhaalfacturen: `php /pad/naar/cron.php` of `curl -s https://jouwdomein.nl/cron/<cron-sleutel>` (sleutel staat bij Instellingen > Facturatie).

## Demo-login (seed)

| Rol | E-mail | Wachtwoord |
|---|---|---|
| Beheerder | beheer@factuurcms.nl | Beheer123! |
| Klant | klant@factuurcms.nl | Klant123! |

Wijzig deze wachtwoorden direct na installatie.

## Lokaal draaien

```bash
mysql -uroot -e "CREATE DATABASE factuurcms; CREATE USER 'factuurcms'@'localhost' IDENTIFIED BY 'factuurcms'; GRANT ALL ON factuurcms.* TO 'factuurcms'@'localhost';"
mysql -ufactuurcms -pfactuurcms factuurcms < Application/Database/schema.sql
mysql -ufactuurcms -pfactuurcms factuurcms < Application/Database/seed.sql
php -S 127.0.0.1:8080 index.php
```

## Structuur

- `Application/Core` – router, request/response, model (PascalCase active record), auth, validator, mailer, PDF
- `Application/Controllers/{Beheer,Klant,Frontend,Auth}`, `Application/Models`, `Application/Services` (Billing, DocumentPdf, Updater)
- `Application/Views/{layouts,beheer,klant,frontend,auth,shared}`
- `Application/Database/schema.sql`, `seed.sql`, `Migrations/*.sql` (nieuwe migraties: oplopend genummerd)
