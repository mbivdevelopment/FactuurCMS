<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use App\Core\Log;
use App\Core\Migrator;
use App\Models\Setting;

/**
 * GitHub-updater: haalt de zipball van repo/branch op (token optioneel, nodig voor private repo's), overschrijft de
 * code en voert daarna de databasemigraties uit. Lokale configuratie, storage en branding worden nooit overschreven.
 */
final class Updater
{
    private const PROTECTED = [
        'Application/Config/database.php',
        'Application/Config/app.php',
        '.htaccess',
    ];
    private const PROTECTED_DIRS = ['storage/', 'Application/Theme/assets/branding/', '.git/'];

    public static function repo(): string
    {
        return trim((string) Setting::get('updater.repo', 'mbivdevelopment/FactuurCMS'), '/ ');
    }

    public static function branch(): string
    {
        return (string) Setting::get('updater.branch', 'main');
    }

    public static function localVersion(): string
    {
        return trim((string) @file_get_contents(App::instance()->path('VERSION'))) ?: '0.0.0';
    }

    public static function installedCommit(): string
    {
        return (string) Setting::get('updater.commit', '');
    }

    /** @return array{sha: string, message: string, date: string, version: string} */
    public static function latest(): array
    {
        $commit = self::api('/repos/' . self::repo() . '/commits/' . rawurlencode(self::branch()));
        $data = json_decode($commit, true);
        if (!is_array($data) || !isset($data['sha'])) {
            throw new \RuntimeException('Kon de laatste versie niet ophalen: ' . (is_array($data) ? (string) ($data['message'] ?? 'onbekende fout') : 'ongeldig antwoord'));
        }
        $version = '';
        try {
            $raw = json_decode(self::api('/repos/' . self::repo() . '/contents/VERSION?ref=' . rawurlencode(self::branch())), true);
            $version = is_array($raw) && isset($raw['content']) ? trim((string) base64_decode((string) $raw['content'])) : '';
        } catch (\Throwable) {
        }

        return [
            'sha' => (string) $data['sha'],
            'message' => strtok((string) ($data['commit']['message'] ?? ''), "\n") ?: '',
            'date' => (string) ($data['commit']['committer']['date'] ?? ''),
            'version' => $version,
        ];
    }

    /** @return array{files: int, migrations: list<string>, sha: string} */
    public static function install(): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('De PHP-extensie zip ontbreekt op deze server.');
        }
        $latest = self::latest();
        $root = App::instance()->path();
        $tmp = $root . '/storage/cache/update-' . bin2hex(random_bytes(4));
        @mkdir($tmp, 0775, true);
        $zipFile = $tmp . '/update.zip';
        file_put_contents($zipFile, self::api('/repos/' . self::repo() . '/zipball/' . $latest['sha'], 120));

        $zip = new \ZipArchive();
        if ($zip->open($zipFile) !== true) {
            self::cleanup($tmp);
            throw new \RuntimeException('Het updatebestand kon niet worden geopend.');
        }
        $files = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            $relative = substr($name, (int) strpos($name, '/') + 1);
            if ($relative === '' || str_ends_with($relative, '/') || str_contains($relative, '..') || self::isProtected($relative)) {
                continue;
            }
            $target = $root . '/' . $relative;
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0775, true);
            }
            $content = $zip->getFromIndex($i);
            if ($content === false) {
                continue;
            }
            if (!is_file($target) || hash('sha256', $content) !== hash_file('sha256', $target)) {
                file_put_contents($target, $content);
                $files++;
            }
        }
        $zip->close();
        self::cleanup($tmp);
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        $migrations = (new Migrator($root))->migrate();
        Setting::set('updater.commit', $latest['sha']);
        Setting::set('updater.bijgewerkt', now());
        Log::db('update', 'Bijgewerkt naar ' . substr($latest['sha'], 0, 7) . " ($files bestanden)");

        return ['files' => $files, 'migrations' => $migrations, 'sha' => $latest['sha']];
    }

    public static function isProtected(string $relative): bool
    {
        if (in_array($relative, self::PROTECTED, true)) {
            return true;
        }
        foreach (self::PROTECTED_DIRS as $dir) {
            if (str_starts_with($relative, $dir)) {
                return true;
            }
        }

        return false;
    }

    private static function api(string $path, int $timeout = 20): string
    {
        $headers = ['User-Agent: FactuurCMS-Updater', 'Accept: application/vnd.github+json'];
        $token = Setting::getSecret('updater.token');
        if ($token !== '') {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        $ch = curl_init('https://api.github.com' . $path);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => $timeout, CURLOPT_HTTPHEADER => $headers]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($body === false) {
            throw new \RuntimeException('GitHub niet bereikbaar: ' . $error);
        }
        if ($status >= 400) {
            $msg = json_decode((string) $body, true)['message'] ?? 'HTTP ' . $status;
            throw new \RuntimeException('GitHub: ' . $msg . ($status === 404 ? ' (klopt de repo/branch, en is een token nodig?)' : ''));
        }

        return (string) $body;
    }

    private static function cleanup(string $dir): void
    {
        foreach (glob($dir . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($dir);
    }
}
