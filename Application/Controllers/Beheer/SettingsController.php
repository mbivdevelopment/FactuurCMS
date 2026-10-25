<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Models\Setting;

final class SettingsController extends Controller
{
    /** Toegestane sleutels per tab; *wachtwoord/token worden versleuteld opgeslagen. */
    public const KEYS = [
        'bedrijf' => ['bedrijf.naam', 'bedrijf.adres', 'bedrijf.postcode', 'bedrijf.plaats', 'bedrijf.land', 'bedrijf.email', 'bedrijf.telefoon', 'bedrijf.website', 'bedrijf.kvk', 'bedrijf.btw', 'bedrijf.iban', 'bedrijf.bic'],
        'branding' => ['branding.kleur'],
        'facturatie' => ['facturatie.prefix_factuur', 'facturatie.prefix_offerte', 'facturatie.betaaltermijn', 'facturatie.offerte_geldig', 'facturatie.btw', 'facturatie.dagen_vooraf', 'facturatie.voettekst', 'facturatie.voettekst_offerte', 'cron.sleutel'],
        'email' => ['mail.methode', 'mail.afzender', 'mail.afzendernaam', 'mail.host', 'mail.poort', 'mail.beveiliging', 'mail.gebruiker'],
        'website' => ['website.titel', 'website.intro', 'app.url', 'topbar.telefoon', 'topbar.email', 'topbar.slogan', 'links.webmail', 'links.paneel', 'home.label', 'home.titel', 'home.tekst', 'hero.titel', 'hero.tekst', 'hero.extensies', 'over.titel', 'over.tekst', 'over.wie', 'over.hoe', 'over.missie', 'footer.tekst', 'social.linkedin', 'social.facebook', 'social.instagram'],
    ];
    private const BRANDING = ['logo' => 'Logo', 'logo_licht' => 'Logo (lichte variant, donkere achtergrond)', 'favicon' => 'Favicon', 'over' => 'Afbeelding "Over ons" (homepage)', 'hero' => 'Hero-foto homepage (bijv. server, liefst vrijstaand PNG)'];

    public function index(Request $request): Response
    {
        if ((string) setting('cron.sleutel', '') === '') {
            Setting::set('cron.sleutel', bin2hex(random_bytes(12)));
        }

        return $this->view('beheer.settings', ['brandingKinds' => self::BRANDING, 'hasMailPassword' => Setting::getSecret('mail.wachtwoord') !== '']);
    }

    public function update(Request $request): Response
    {
        $tab = $request->string('tab');
        foreach (self::KEYS[$tab] ?? [] as $key) {
            $field = str_replace('.', '_', $key);
            if ($request->has($field)) {
                Setting::set($key, trim($request->string($field)));
            }
        }
        if ($tab === 'email' && $request->string('mail_wachtwoord') !== '') {
            Setting::setSecret('mail.wachtwoord', $request->string('mail_wachtwoord'));
        }
        if ($tab === 'branding' && !preg_match('/^#[0-9a-f]{6}$/i', (string) setting('branding.kleur', ''))) {
            Setting::set('branding.kleur', '#ff5a1f');
        }

        return $this->redirect('/beheer/instellingen#' . $tab)->with('success', 'Instellingen opgeslagen.');
    }

    public function branding(Request $request): Response
    {
        $kind = $request->string('soort');
        $file = $request->file('bestand');
        if (!isset(self::BRANDING[$kind]) || $file === null || ($file['error'] ?? 1) !== UPLOAD_ERR_OK) {
            return $this->redirect('/beheer/instellingen#branding')->with('error', 'Kies een bestand om te uploaden.');
        }
        if ((int) $file['size'] > 2 * 1024 * 1024) {
            return $this->redirect('/beheer/instellingen#branding')->with('error', 'Het bestand mag maximaal 2 MB zijn.');
        }
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $mime = (string) (mime_content_type((string) $file['tmp_name']) ?: '');
        $allowed = ['png' => ['image/png'], 'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'webp' => ['image/webp'], 'svg' => ['image/svg+xml', 'text/xml', 'text/plain', 'image/svg'], 'ico' => ['image/x-icon', 'image/vnd.microsoft.icon']];
        if (!isset($allowed[$ext]) || !in_array($mime, $allowed[$ext], true)) {
            return $this->redirect('/beheer/instellingen#branding')->with('error', 'Alleen PNG, JPG, WEBP, SVG of ICO is toegestaan.');
        }
        if ($ext === 'svg' && preg_match('/<script|on\w+\s*=|javascript:/i', (string) file_get_contents((string) $file['tmp_name']))) {
            return $this->redirect('/beheer/instellingen#branding')->with('error', 'Deze SVG bevat scripts en is geweigerd.');
        }
        $dir = App::instance()->path('Application/Theme/assets/branding');
        $old = (string) setting('branding.' . $kind, '');
        if ($old !== '' && is_file($dir . '/' . basename($old))) {
            @unlink($dir . '/' . basename($old));
        }
        $name = $kind . '-' . bin2hex(random_bytes(4)) . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        if (!move_uploaded_file((string) $file['tmp_name'], $dir . '/' . $name)) {
            return $this->redirect('/beheer/instellingen#branding')->with('error', 'Opslaan van het bestand is mislukt (schrijfrechten?).');
        }
        Setting::set('branding.' . $kind, $name);

        return $this->redirect('/beheer/instellingen#branding')->with('success', self::BRANDING[$kind] . ' is bijgewerkt.');
    }

    public function removeBranding(Request $request): Response
    {
        $kind = $request->string('soort');
        if (isset(self::BRANDING[$kind])) {
            $old = basename((string) setting('branding.' . $kind, ''));
            $path = App::instance()->path('Application/Theme/assets/branding/' . $old);
            if ($old !== '' && is_file($path)) {
                @unlink($path);
            }
            Setting::set('branding.' . $kind, '');
        }

        return $this->redirect('/beheer/instellingen#branding')->with('success', 'Verwijderd.');
    }

    public function testMail(Request $request): Response
    {
        $to = $request->string('naar') ?: (string) Auth::user()?->Email;
        $ok = Mailer::send($to, 'Testmail van ' . company_name(), "Dit is een testmail.\n\nDe e-mailinstellingen werken.");

        return $this->redirect('/beheer/instellingen#email')->with($ok ? 'success' : 'error', $ok ? 'Testmail verstuurd naar ' . $to . '.' : 'Versturen mislukt: ' . (Mailer::$lastError ?? 'onbekend'));
    }
}
