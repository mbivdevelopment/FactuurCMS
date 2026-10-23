<?php

declare(strict_types=1);

namespace App\Controllers\Frontend;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Product;
use App\Services\Billing;

final class SiteController extends Controller
{
    public function home(Request $request): Response
    {
        return $this->view('frontend.home', [
            'featured' => Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 ORDER BY Uitgelicht DESC, Volgorde, Id LIMIT 3'),
            'groups' => Database::all('SELECT Groep, COUNT(*) AS Aantal, MIN(Prijs) AS Vanaf FROM Producten WHERE Zichtbaar = 1 GROUP BY Groep ORDER BY MIN(Volgorde)'),
        ]);
    }

    public function services(Request $request): Response
    {
        $group = $request->string('groep');
        $products = $group !== ''
            ? Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Groep = ? ORDER BY Volgorde, Id', [$group])
            : Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 ORDER BY Volgorde, Id');

        return $this->view('frontend.services', [
            'products' => $products,
            'group' => $group,
            'groups' => array_column(Database::all('SELECT DISTINCT Groep FROM Producten WHERE Zichtbaar = 1 ORDER BY Groep'), 'Groep'),
        ]);
    }

    public function product(Request $request): Response
    {
        return $this->view('frontend.product', ['product' => $this->findProduct($request)]);
    }

    public function order(Request $request): Response
    {
        $product = $this->findProduct($request);
        $user = Auth::user();
        if ($user === null) {
            Session::set('intended', '/diensten/' . $product->Slug);

            return $this->redirect('/registreren')->with('success', 'Maak een account aan of log in om ' . $product->Naam . ' te bestellen.');
        }
        if ($user->isStaff()) {
            return Response::back('/diensten')->with('error', 'Log in als klant om te bestellen (of maak de dienst aan in het beheer).');
        }
        if ($request->string('Akkoord') === '') {
            return Response::back('/diensten')->with('error', 'Ga akkoord met de voorwaarden om te bestellen.');
        }
        $doc = Billing::order($user, $product, $request->string('Notitie'));
        Billing::send($doc);
        $admin = (string) setting('bedrijf.email', '');
        if ($admin !== '') {
            Mailer::send($admin, 'Nieuwe bestelling: ' . $product->Naam, "Klant {$user->display()} ({$user->Nummer}) heeft {$product->Naam} besteld.\nFactuur: {$doc->Nummer}\n" . absolute_url('/beheer/documenten/' . $doc->id()));
        }

        return $this->redirect('/klant/documenten/' . $doc->id())->with('success', 'Bedankt voor je bestelling! De dienst wordt geactiveerd zodra de factuur is betaald.');
    }

    public function contact(Request $request): Response
    {
        return $this->view('frontend.contact');
    }

    public function sendContact(Request $request): Response
    {
        $data = $this->validate($request, ['Naam' => 'required|max:100', 'Email' => 'required|email', 'Bericht' => 'required|max:5000']);
        if ($request->string('website') !== '') {
            return $this->redirect('/contact');
        }
        Mailer::send((string) setting('bedrijf.email', 'info@localhost'), 'Contactformulier: ' . $data['Naam'], "Naam: {$data['Naam']}\nE-mail: {$data['Email']}\n\n{$data['Bericht']}");

        return $this->redirect('/contact')->with('success', 'Bedankt voor je bericht, we nemen snel contact op.');
    }

    /** Cronjob via URL (bv. dagelijks): /cron/{sleutel} uit Instellingen → Facturatie. */
    public function cron(Request $request): Response
    {
        $key = (string) setting('cron.sleutel', '');
        if ($key === '' || !hash_equals($key, (string) $request->param('key'))) {
            throw new HttpException(404, '');
        }

        return Response::html('Herhaalfacturen aangemaakt: ' . Billing::runRecurring());
    }

    private function findProduct(Request $request): Product
    {
        $product = Product::firstWhere(['Slug' => (string) $request->param('slug'), 'Zichtbaar' => 1]);
        if ($product === null) {
            throw new HttpException(404, 'Deze dienst bestaat niet (meer).');
        }

        return $product;
    }
}
