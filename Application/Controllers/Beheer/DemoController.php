<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\DemoData;

final class DemoController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->redirect('/beheer/updates#demo');
    }

    public function destroy(Request $request): Response
    {
        $n = DemoData::remove();

        return $this->redirect('/beheer/updates#demo')->with('success', $n > 0
            ? 'Demo-data verwijderd (' . $n . ' records). De volgende nieuwe klant krijgt nummer ' . User::nextNumber('K') . '.'
            : 'Er stond geen demo-data meer.');
    }
}
