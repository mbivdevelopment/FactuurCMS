<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\App;
use App\Core\Controller;
use App\Core\Migrator;
use App\Core\Request;
use App\Core\Response;
use App\Models\Setting;
use App\Services\Updater;

final class UpdateController extends Controller
{
    public function index(Request $request): Response
    {
        $latest = null;
        $error = null;
        if ($request->string('controleren') !== '') {
            try {
                $latest = Updater::latest();
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return $this->view('beheer.updates', ['latest' => $latest, 'error' => $error, 'hasToken' => Setting::getSecret('updater.token') !== '']);
    }

    public function save(Request $request): Response
    {
        Setting::set('updater.repo', trim($request->string('repo'), '/ '));
        Setting::set('updater.branch', $request->string('branch') ?: 'main');
        if ($request->bool('token_wissen')) {
            Setting::setSecret('updater.token', '');
        } elseif ($request->string('token') !== '') {
            Setting::setSecret('updater.token', $request->string('token'));
        }

        return $this->redirect('/beheer/updates')->with('success', 'Updater-instellingen opgeslagen.');
    }

    public function install(Request $request): Response
    {
        try {
            $result = Updater::install();
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/updates')->with('error', 'Update mislukt: ' . $e->getMessage());
        }

        return $this->redirect('/beheer/updates')->with('success', 'Bijgewerkt naar ' . substr($result['sha'], 0, 7) . ': ' . $result['files'] . ' bestand(en) vernieuwd. ' . implode(' ', $result['migrations']));
    }

    public function migrate(Request $request): Response
    {
        try {
            $messages = (new Migrator(App::instance()->path()))->migrate();
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/updates')->with('error', 'Migreren mislukt: ' . $e->getMessage());
        }

        return $this->redirect('/beheer/updates')->with('success', implode(' ', $messages));
    }
}
