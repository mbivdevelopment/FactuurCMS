<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\ValidationException;
use App\Services\Content;

/** Projectoverzicht met taken, uren en budget. */
final class ProjectController extends Controller
{
    public const TASK_STATUSES = ['open' => 'Open', 'bezig' => 'Bezig', 'klaar' => 'Klaar'];

    public function show(Request $request): Response
    {
        $project = $this->project($request);
        $tasks = Database::all("SELECT * FROM ProjectTaken WHERE ProjectId = ? ORDER BY FIELD(Status, 'bezig', 'open', 'klaar'), Deadline IS NULL, Deadline, Id", [(int) $project['Id']]);
        $estimated = array_sum(array_map(static fn (array $t): float => (float) $t['GeschatteUren'], $tasks));
        $worked = array_sum(array_map(static fn (array $t): float => (float) $t['GewerkteUren'], $tasks));
        $rate = (int) $project['Uurtarief'];

        return $this->view('beheer.projects.show', [
            'project' => $project,
            'tasks' => $tasks,
            'staff' => self::staff(),
            'customer' => $project['KlantId'] ? (Content::customers()[(int) $project['KlantId']] ?? null) : null,
            'totals' => [
                'estimated' => $estimated,
                'worked' => $worked,
                'done' => count(array_filter($tasks, static fn (array $t): bool => $t['Status'] === 'klaar')),
                'estimatedValue' => (int) round($estimated * $rate),
                'workedValue' => (int) round($worked * $rate),
            ],
        ]);
    }

    public function storeTask(Request $request): Response
    {
        $project = $this->project($request);
        $data = $this->taskInput($request);
        $data['ProjectId'] = (int) $project['Id'];
        $data['AangemaaktOp'] = now();
        Database::insert('ProjectTaken', $data);

        return $this->redirect($this->base($project))->with('success', 'Taak toegevoegd.');
    }

    public function updateTask(Request $request): Response
    {
        $project = $this->project($request);
        $task = $this->task($request, $project);
        $data = $this->taskInput($request);
        $data['BijgewerktOp'] = now();
        Database::update('ProjectTaken', $data, ['Id' => (int) $task['Id']]);

        return $this->redirect($this->base($project))->with('success', 'Taak opgeslagen.');
    }

    public function destroyTask(Request $request): Response
    {
        $project = $this->project($request);
        $task = $this->task($request, $project);
        Database::delete('ProjectTaken', ['Id' => (int) $task['Id']]);

        return $this->redirect($this->base($project))->with('success', 'Taak verwijderd.');
    }

    /** @return array<int, string> */
    public static function staff(): array
    {
        $list = [];
        foreach (Database::all("SELECT Id, Voornaam, Achternaam FROM Gebruikers WHERE Rol IN ('beheerder', 'medewerker') ORDER BY Voornaam, Achternaam") as $u) {
            $list[(int) $u['Id']] = trim($u['Voornaam'] . ' ' . $u['Achternaam']);
        }

        return $list;
    }

    /** @return array<string, mixed> */
    private function taskInput(Request $request): array
    {
        $title = trim($request->string('Titel'));
        if ($title === '') {
            throw new ValidationException(['Titel' => 'Geef de taak een titel.'], $request);
        }
        $status = $request->string('Status');
        $staff = $request->int('MedewerkerId');
        $deadline = $request->string('Deadline');
        $hours = static fn (string $v): float => max(0.0, min(99999.0, round((float) str_replace(',', '.', trim($v)), 2)));
        $note = trim($request->string('Omschrijving'));

        return [
            'Titel' => mb_substr($title, 0, 150),
            'Omschrijving' => $note === '' ? null : mb_substr($note, 0, 60000),
            'Status' => isset(self::TASK_STATUSES[$status]) ? $status : 'open',
            'MedewerkerId' => isset(self::staff()[$staff]) ? $staff : null,
            'Deadline' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline) ? $deadline : null,
            'GeschatteUren' => $hours($request->string('GeschatteUren')),
            'GewerkteUren' => $hours($request->string('GewerkteUren')),
        ];
    }

    /** @param array<string, mixed> $project */
    private function base(array $project): string
    {
        return '/beheer/projecten/' . (int) $project['Id'] . '/taken';
    }

    /** @return array<string, mixed> */
    private function project(Request $request): array
    {
        return Database::first('SELECT * FROM Projecten WHERE Id = ?', [(int) $request->param('id')]) ?? throw new HttpException(404, 'Project niet gevonden.');
    }

    /**
     * @param array<string, mixed> $project
     * @return array<string, mixed>
     */
    private function task(Request $request, array $project): array
    {
        return Database::first('SELECT * FROM ProjectTaken WHERE Id = ? AND ProjectId = ?', [(int) $request->param('taak'), (int) $project['Id']]) ?? throw new HttpException(404, 'Taak niet gevonden.');
    }
}
