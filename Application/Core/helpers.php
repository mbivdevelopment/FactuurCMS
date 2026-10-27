<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Auth;
use App\Core\Config;
use App\Core\Session;

function now(): string
{
    return date('Y-m-d H:i:s');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

function setting(string $key, mixed $default = null): mixed
{
    return \App\Models\Setting::get($key, $default);
}

/** URL-prefix wanneer de app in een submap draait (leeg in de webroot). */
function base_path_url(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));

    return str_ends_with($script, '/index.php') ? rtrim(dirname($script), '/') : '';
}

function url(string $path = '/'): string
{
    return base_path_url() . '/' . ltrim($path, '/');
}

/** Volledige URL (voor mails/pdf): Instellingen → app.url of de huidige host. */
function absolute_url(string $path = '/'): string
{
    if (preg_match('#^https?://#', $path) === 1) {
        return $path;
    }
    $base = (string) setting('app.url', config('app.url', ''));
    if ($base === '') {
        $https = ($_SERVER['HTTPS'] ?? '') !== '' && $_SERVER['HTTPS'] !== 'off';
        $base = ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . base_path_url();
    }

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $file = App::instance()->path('Application/Theme/assets/' . ltrim($path, '/'));

    return url('assets/' . ltrim($path, '/')) . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/** Geüploade brandingbestanden (logo, logo_licht, favicon) in Application/Theme/assets/branding. */
function branding_url(string $kind): string
{
    $file = (string) setting('branding.' . $kind, '');

    return $file !== '' && is_file(App::instance()->path('Application/Theme/assets/branding/' . $file)) ? url('assets/branding/' . $file) : '';
}

function branding_path(string $kind): string
{
    $file = (string) setting('branding.' . $kind, '');
    $path = App::instance()->path('Application/Theme/assets/branding/' . $file);

    return $file !== '' && is_file($path) ? $path : '';
}

/** Geüpload (uploads/...) of meegeleverd (img/...) mediabestand in Application/Theme/assets; leeg = geen. */
function media_url(?string $path): string
{
    $path = ltrim((string) $path, '/');

    return $path !== '' && !str_contains($path, '..') && is_file(App::instance()->path('Application/Theme/assets/' . $path)) ? asset($path) : '';
}

/** Platte tekst naar veilige HTML: lege regel = alinea, "## " = kop, "- " = opsomming. */
function rich_text(?string $text): string
{
    $html = '';
    foreach (preg_split('/\R{2,}/', trim((string) $text)) ?: [] as $block) {
        $lines = preg_split('/\R/', trim($block)) ?: [];
        if ($lines === [] || $lines[0] === '') {
            continue;
        }
        if (str_starts_with($lines[0], '## ')) {
            $html .= '<h2>' . e(substr(array_shift($lines), 3)) . '</h2>';
        }
        $items = array_filter($lines, static fn (string $l): bool => str_starts_with($l, '- '));
        if ($lines !== [] && count($items) === count($lines)) {
            $html .= '<ul>' . implode('', array_map(static fn (string $l): string => '<li>' . e(substr($l, 2)) . '</li>', $lines)) . '</ul>';
        } elseif ($lines !== []) {
            $html .= '<p>' . nl2br(e(implode("\n", $lines))) . '</p>';
        }
    }

    return $html;
}

function company_name(): string
{
    return (string) setting('bedrijf.naam', config('app.name', 'FactuurCMS'));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Session::token()) . '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}

function old(string $key, mixed $default = ''): mixed
{
    $old = Session::getFlash('old', []);

    return is_array($old) && array_key_exists($key, $old) ? $old[$key] : $default;
}

/** @return array<string, string> */
function errors(): array
{
    $errors = Session::getFlash('errors', []);

    return is_array($errors) ? $errors : [];
}

function error(string $key): ?string
{
    return errors()[$key] ?? null;
}

function flash(string $key, mixed $default = null): mixed
{
    return Session::getFlash($key, $default);
}

function auth_user(): ?\App\Models\User
{
    return Auth::user();
}

/** Bedragen staan in centen. */
function money(int $cents): string
{
    return '€ ' . number_format($cents / 100, 2, ',', '.');
}

/** "12,50" / "12.50" / "1.250,00" → centen. */
function parse_money(mixed $value): int
{
    $s = trim(str_replace(['€', ' '], '', (string) $value));
    if ($s === '') {
        return 0;
    }
    if (str_contains($s, ',')) {
        $s = str_replace(['.', ','], ['', '.'], $s);
    }

    return (int) round((float) $s * 100);
}

function cents_input(int $cents): string
{
    return number_format($cents / 100, 2, ',', '');
}

function slugify(string $text): string
{
    $text = strtolower(trim((string) iconv('UTF-8', 'ASCII//TRANSLIT', $text)));

    return trim((string) preg_replace('/[^a-z0-9]+/', '-', $text), '-');
}

function nl_date(?string $datetime, bool $withTime = false): string
{
    $ts = $datetime === null || $datetime === '' ? false : strtotime($datetime);

    return $ts === false ? '-' : date($withTime ? 'd-m-Y H:i' : 'd-m-Y', $ts);
}

function nl_month(string $ym): string
{
    $months = ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];

    return $months[(int) substr($ym, 5, 2) - 1] ?? $ym;
}

function period_label(string $period): string
{
    return ['eenmalig' => 'eenmalig', 'maand' => 'per maand', 'kwartaal' => 'per kwartaal', 'jaar' => 'per jaar', '2jaar' => 'per 2 jaar', '4jaar' => 'per 4 jaar'][$period] ?? $period;
}

/** Subtype van een dienst (categorienaam, bijv. 'Domeinnamen' of 'Reseller hosting'). */
function dienst_type(array|\App\Models\Service $s): string
{
    $bron = (string) (is_array($s) ? ($s['Bron'] ?? '') : ($s->Bron ?? ''));
    if (in_array($bron, ['domein', 'realtimeregister'], true)) {
        return 'Domeinnamen';
    }
    $soort = (string) (is_array($s) ? ($s['Dienstsoort'] ?? '') : ($s->Dienstsoort ?? ''));
    if ($soort !== '') {
        foreach (\App\Models\Service::CATEGORIES as $def) {
            if (in_array($soort, $def[1], true)) {
                return $def[0];
            }
        }

        return $soort;
    }

    return '';
}

/** Weergavenaam van een dienst: bij domeindiensten de domeinnaam zelf, anders de dienstnaam. */
function dienst_naam(array|\App\Models\Service $s): string
{
    $naam = (string) (is_array($s) ? ($s['Naam'] ?? '') : $s->Naam);
    $oms = trim((string) (is_array($s) ? ($s['Omschrijving'] ?? '') : $s->Omschrijving));
    $gv = is_array($s) ? ($s['Gegevens'] ?? []) : $s->Gegevens;
    if (is_string($gv)) {
        $gv = (array) json_decode($gv, true);
    }
    $dom = is_array($gv) ? ($gv['domein'] ?? null) : null;
    $domein = is_array($dom) ? (string) ($dom['naam'] ?? '') : (string) ($dom ?? '');
    if ($domein !== '') {
        return $domein;
    }

    return $oms !== '' && preg_match('/^[a-z0-9][a-z0-9.-]*\.[a-z]{2,}$/i', $oms) ? $oms : $naam;
}

/** Statuslabel als badge met kleur. */
function badge(string $status, string $label): string
{
    $tone = match ($status) {
        'actief', 'betaald', 'geaccepteerd', 'beantwoord', 'ja', 'klaar' => 'ok',
        'vervallen', 'afgewezen', 'opgezegd', 'hoog', 'nee' => 'bad',
        'open', 'in_afwachting', 'verzonden', 'opgeschort', 'bezig' => 'warn',
        default => 'off',
    };

    return '<span class="badge is-' . $tone . '">' . e($label) . '</span>';
}

/** Groen vinkje / rood kruisje met tooltip. */
function check_icon(bool $ok, string $tip = ''): string
{
    return '<span class="check ' . ($ok ? 'is-ok' : 'is-bad') . '" data-tip="' . e($tip !== '' ? $tip : ($ok ? 'Ja' : 'Nee')) . '">' . icon($ok ? 'check' : 'x', 'icon icon-sm') . '</span>';
}

/** Verwijderknop met bevestigingspop-up. */
function delete_button(string $action, string $message, string $label = 'Verwijderen'): string
{
    return '<form method="post" action="' . e($action) . '" data-confirm="' . e($message) . '" class="inline">' . csrf_field() . method_field('DELETE')
        . '<button type="submit" class="btn btn-danger-ghost">' . icon('trash') . ' ' . e($label) . '</button></form>';
}

/**
 * Formulierveld met icoon links en onderlijn.
 * @param array<string, string> $options voor type=select
 */
function field(string $name, string $label, string $icon, mixed $value = '', string $type = 'text', array $options = [], string $extra = ''): string
{
    $value = old($name, $value);
    $id = 'f-' . preg_replace('/[^a-z0-9]+/i', '-', $name);
    $html = '<div class="field' . (error($name) !== null ? ' has-error' : '') . ($type === 'textarea' ? ' field-full' : '') . '"><label for="' . e($id) . '">' . e($label) . '</label><div class="field-control">' . icon($icon);
    if ($type === 'select') {
        $html .= '<select id="' . e($id) . '" name="' . e($name) . '" ' . $extra . '>';
        foreach ($options as $key => $text) {
            $html .= '<option value="' . e($key) . '"' . ((string) $key === (string) $value ? ' selected' : '') . '>' . e($text) . '</option>';
        }
        $html .= '</select>';
    } elseif ($type === 'textarea') {
        $html .= '<textarea id="' . e($id) . '" name="' . e($name) . '" rows="4" ' . $extra . '>' . e($value) . '</textarea>';
    } else {
        $html .= '<input id="' . e($id) . '" type="' . e($type) . '" name="' . e($name) . '" value="' . e($value) . '" ' . $extra . '>';
    }
    $html .= '</div>' . (error($name) !== null ? '<small class="field-error">' . e(error($name)) . '</small>' : '') . '</div>';

    return $html;
}

/** Paginering-links voor Database::page(). @param array{page: int, pages: int} $page */
function pagination(array $page): string
{
    if ($page['pages'] <= 1) {
        return '';
    }
    $query = $_GET;
    $html = '<nav class="pagination">';
    for ($i = 1; $i <= $page['pages']; $i++) {
        $query['page'] = $i;
        $html .= '<a href="?' . e(http_build_query($query)) . '"' . ($i === $page['page'] ? ' class="active"' : '') . '>' . $i . '</a>';
    }

    return $html . '</nav>';
}

/** Inline SVG-icoon (lijnstijl 24x24). */
function icon(string $name, string $class = 'icon'): string
{
    static $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18.5 14a6.5 6.5 0 0 1 3 6"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'server' => '<rect x="3" y="4" width="18" height="7" rx="2"/><rect x="3" y="13" width="18" height="7" rx="2"/><path d="M7 7.5h.01M7 16.5h.01"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
        'cpu' => '<rect x="5" y="5" width="14" height="14" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'layout' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
        'box' => '<path d="M21 8 12 3 3 8v8l9 5 9-5zM3 8l9 5 9-5M12 13v8"/>',
        'file' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8zM14 3v5h5M9 13h6M9 17h6"/>',
        'quote' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8zM14 3v5h5M9 15l2 2 4-4"/>',
        'ticket' => '<path d="M4 6h16a1 1 0 0 1 1 1v3a2 2 0 0 0 0 4v3a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-3a2 2 0 0 0 0-4V7a1 1 0 0 1 1-1zM9 6v12"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M2 12h3M19 12h3M4.9 19.1 7 17M17 7l2.1-2.1"/>',
        'refresh' => '<path d="M20 11a8 8 0 0 0-14.9-3.5M4 4v4h4M4 13a8 8 0 0 0 14.9 3.5M20 20v-4h-4"/>',
        'euro' => '<path d="M18 6.5A7 7 0 0 0 6.3 9M18 17.5A7 7 0 0 1 6.3 15M3 10.5h11M3 13.5h11"/>',
        'wallet' => '<path d="M3 7a2 2 0 0 1 2-2h13v3H5a2 2 0 0 1-2-2zm0 0v11a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1H5"/><circle cx="16.5" cy="14" r="1"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'check' => '<path d="m5 12 5 5 9-10"/>',
        'x' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'trash' => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
        'edit' => '<path d="M4 20h4L19 9l-4-4L4 16zM14 6l4 4"/>',
        'download' => '<path d="M12 4v12M7 11l5 5 5-5M4 20h16"/>',
        'send' => '<path d="M21 3 10 14M21 3l-7 18-4-7-7-4z"/>',
        'logout' => '<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4M10 8l-4 4 4 4M6 12h10"/>',
        'login' => '<path d="M9 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h4M14 8l4 4-4 4M18 12H8"/>',
        'eye' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'building' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
        'map' => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'hash' => '<path d="M4 9h16M4 15h16M10 3 8 21M16 3l-2 18"/>',
        'lock' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'tag' => '<path d="M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9z"/><circle cx="8" cy="8" r="1.5"/>',
        'palette' => '<path d="M12 3a9 9 0 1 0 0 18c1.4 0 2-.9 2-2 0-.7-.4-1-.4-1.7 0-1 .8-1.8 1.8-1.8H17a4 4 0 0 0 4-4c0-4.7-4-8.5-9-8.5z"/><circle cx="7.5" cy="12" r="1"/><circle cx="9.5" cy="7.5" r="1"/><circle cx="14.5" cy="7.5" r="1"/>',
        'image' => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path d="m21 16-5-5-9 8"/>',
        'github' => '<path d="M9 19c-4 1.5-4-2-6-2.5M15 21v-3.5c0-1 .1-1.4-.5-2 2.8-.3 5.5-1.4 5.5-6a4.6 4.6 0 0 0-1.3-3.2 4.2 4.2 0 0 0-.1-3.2s-1.1-.3-3.5 1.3a12 12 0 0 0-6.2 0C6.5 2.8 5.4 3.1 5.4 3.1a4.2 4.2 0 0 0-.1 3.2A4.6 4.6 0 0 0 4 9.5c0 4.6 2.7 5.7 5.5 6-.6.6-.6 1.2-.5 2V21"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'chat' => '<path d="M21 12a8 8 0 0 1-11.6 7.2L4 20l1-4.6A8 8 0 1 1 21 12z"/>',
        'shield' => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z"/><path d="m9 12 2 2 4-4"/>',
        'zap' => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
        'headset' => '<path d="M4 14v-2a8 8 0 0 1 16 0v2M4 14h3v6H5a1 1 0 0 1-1-1zM20 14h-3v6h2a1 1 0 0 0 1-1z"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'cart' => '<circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/><path d="M3 4h2l2.5 11h11L21 8H6.2"/>',
        'copy' => '<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3"/>',
        'note' => '<path d="M5 4h14v12l-4 4H5zM15 20v-4h4M9 9h6M9 13h4"/>',
        'link' => '<path d="M10 14a4 4 0 0 0 6 0l3-3a4 4 0 0 0-6-6l-1 1M14 10a4 4 0 0 0-6 0l-3 3a4 4 0 0 0 6 6l1-1"/>',
        'home' => '<path d="m3 11 9-8 9 8M5 9.5V21h14V9.5"/>',
        'cloud' => '<path d="M7 18a5 5 0 0 1-.9-9.9A6 6 0 0 1 17.6 7 4.5 4.5 0 0 1 17.5 18z"/>',
        'code' => '<path d="m8 8-4 4 4 4M16 8l4 4-4 4M14 4l-4 16"/>',
        'trend' => '<path d="m3 17 6-6 4 4 8-8M15 7h6v6"/>',
        'book' => '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2zM4 19V5M19 19v2H6"/>',
        'pin' => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'star' => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
        'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
        'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        'heart' => '<path d="M12 20s-7.5-4.6-9-9.3C2 7.3 4.3 4.5 7.3 4.5c2 0 3.6 1.1 4.7 2.7 1.1-1.6 2.7-2.7 4.7-2.7 3 0 5.3 2.8 4.3 6.2C19.5 15.4 12 20 12 20z"/>',
        'flag' => '<path d="M5 21V4M5 4h11l-2 4 2 4H5"/>',
        'handshake' => '<path d="m11 17 2 2a1.4 1.4 0 0 0 2-2M14 14l2.5 2.5a1.4 1.4 0 0 0 2-2L15 11M3 13l5-5 3 1 3-3 7 7-2 2M3 13l5 5 1-1M8 8 4 4M18 6l2-2"/>',
        'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
        'facebook' => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
        'webmail' => '<path d="M3 8l9 6 9-6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/><path d="M15 15h4"/>',
    ];
    $svg = $icons[$name] ?? '<circle cx="12" cy="12" r="8"/>';

    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $svg . '</svg>';
}
