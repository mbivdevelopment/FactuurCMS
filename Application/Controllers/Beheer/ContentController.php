<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\App;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Services\Content;

/** Generiek beheer van de website-modules uit App\Services\Content. */
final class ContentController extends Controller
{
    private const IMAGES = ['png' => ['image/png'], 'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'webp' => ['image/webp'], 'gif' => ['image/gif'], 'svg' => ['image/svg+xml', 'text/xml', 'text/plain', 'image/svg']];

    public function index(Request $request): Response
    {
        $type = $this->type($request);

        return $this->view('beheer.content.index', ['type' => $type, 'rows' => Database::all('SELECT * FROM `' . $type['table'] . '` ORDER BY ' . $type['order'])]);
    }

    public function create(Request $request): Response
    {
        $type = $this->type($request);
        $row = ['Zichtbaar' => 1, 'Volgorde' => (int) Database::scalar('SELECT COUNT(*) FROM `' . $type['table'] . '`') + 1, 'PublicatieDatum' => date('Y-m-d'), 'Icoon' => 'server', 'Status' => 'actief', 'Soort' => 'mijlpaal'];

        return $this->view('beheer.content.form', ['type' => $type, 'row' => array_intersect_key($row, $type['fields'])]);
    }

    public function store(Request $request): Response
    {
        $type = $this->type($request);
        $data = $this->input($request, $type, null);
        $data['AangemaaktOp'] = now();
        Database::insert($type['table'], $data);

        return $this->redirect('/beheer/website/' . $type['key'])->with('success', ucfirst($type['single']) . ' is aangemaakt.');
    }

    public function edit(Request $request): Response
    {
        $type = $this->type($request);

        return $this->view('beheer.content.form', ['type' => $type, 'row' => $this->row($request, $type)]);
    }

    public function update(Request $request): Response
    {
        $type = $this->type($request);
        $row = $this->row($request, $type);
        $data = $this->input($request, $type, $row);
        $data['BijgewerktOp'] = now();
        Database::update($type['table'], $data, ['Id' => (int) $row['Id']]);

        return $this->redirect('/beheer/website/' . $type['key'])->with('success', 'Opgeslagen.');
    }

    public function destroy(Request $request): Response
    {
        $type = $this->type($request);
        $row = $this->row($request, $type);
        $this->removeUpload((string) ($row['Afbeelding'] ?? ''));
        Database::delete($type['table'], ['Id' => (int) $row['Id']]);

        return $this->redirect('/beheer/website/' . $type['key'])->with('success', ucfirst($type['single']) . ' verwijderd.');
    }

    /**
     * @param array<string, mixed> $type
     * @param array<string, mixed>|null $row
     * @return array<string, mixed>
     */
    private function input(Request $request, array $type, ?array $row): array
    {
        $rules = [];
        foreach ($type['fields'] as $name => $def) {
            if (($def[3] ?? false) === true) {
                $rules[$name] = 'required';
            }
        }
        $this->validate($request, $rules);

        $data = [];
        foreach ($type['fields'] as $name => $def) {
            $kind = $def[2];
            $value = trim($request->string($name));
            $data[$name] = match ($kind) {
                'bool' => $request->bool($name) ? 1 : 0,
                'number' => (int) $value,
                'decimal' => (float) str_replace(',', '.', $value),
                'select' => array_key_exists($value, $def[4]) ? $value : (string) array_key_first($def[4]),
                'icon' => in_array($value, Content::ICONS, true) ? $value : 'server',
                'date' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : date('Y-m-d'),
                'image' => $this->image($request, $name, $type['key'], (string) ($row[$name] ?? '')),
                default => $value === '' ? null : mb_substr($value, 0, $kind === 'textarea' ? 60000 : 255),
            };
        }
        if (isset($type['fields']['Slug'])) {
            $slug = slugify((string) ($data['Slug'] ?? '') ?: (string) $data[$type['title']]);
            $taken = Database::scalar('SELECT COUNT(*) FROM `' . $type['table'] . '` WHERE Slug = ? AND Id <> ?', [$slug, (int) ($row['Id'] ?? 0)]);
            $data['Slug'] = $taken ? $slug . '-' . substr(bin2hex(random_bytes(2)), 0, 4) : $slug;
        }
        foreach (['Groep', 'Categorie'] as $text) {
            if (array_key_exists($text, $data) && $data[$text] === null && ($type['fields'][$text][2] ?? '') === 'text') {
                $data[$text] = '';
            }
        }

        return $data;
    }

    private function image(Request $request, string $name, string $key, string $current): ?string
    {
        if ($request->bool($name . '_verwijderen')) {
            $this->removeUpload($current);
            $current = '';
        }
        $file = $request->file($name . '_bestand');
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $current === '' ? null : $current;
        }
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $mime = (string) (mime_content_type((string) $file['tmp_name']) ?: '');
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK || (int) $file['size'] > 5 * 1024 * 1024 || !isset(self::IMAGES[$ext]) || !in_array($mime, self::IMAGES[$ext], true)
            || ($ext === 'svg' && preg_match('/<script|on\w+\s*=|javascript:/i', (string) file_get_contents((string) $file['tmp_name'])))) {
            throw new \App\Core\ValidationException([$name => 'Alleen PNG, JPG, WEBP, GIF of SVG tot 5 MB is toegestaan.'], $request);
        }
        $file_name = $key . '-' . bin2hex(random_bytes(5)) . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        if (!move_uploaded_file((string) $file['tmp_name'], App::instance()->path('Application/Theme/assets/uploads/' . $file_name))) {
            throw new \App\Core\ValidationException([$name => 'Opslaan van de afbeelding is mislukt (schrijfrechten op Theme/assets/uploads?).'], $request);
        }
        $this->removeUpload($current);

        return 'uploads/' . $file_name;
    }

    private function removeUpload(string $path): void
    {
        if (str_starts_with($path, 'uploads/') && !str_contains($path, '..')) {
            $file = App::instance()->path('Application/Theme/assets/' . $path);
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /** @return array<string, mixed> */
    private function type(Request $request): array
    {
        return Content::type((string) $request->param('type')) ?? throw new HttpException(404, 'Onbekend onderdeel.');
    }

    /**
     * @param array<string, mixed> $type
     * @return array<string, mixed>
     */
    private function row(Request $request, array $type): array
    {
        return Database::first('SELECT * FROM `' . $type['table'] . '` WHERE Id = ?', [(int) $request->param('id')]) ?? throw new HttpException(404, 'Niet gevonden.');
    }
}
