<?php

declare(strict_types=1);

/** @var \App\Core\Router $router */

// Frontend
$router->group('', ['csrf'], static function ($router): void {
    $router->get('/', 'Frontend\\SiteController@home');
    $router->get('/diensten', 'Frontend\\SiteController@servicesRedirect');
    $router->get('/diensten/{slug}', 'Frontend\\SiteController@product');
    $router->get('/diensten/{slug}/bestellen', 'Frontend\\SiteController@checkout');
    $router->post('/diensten/{slug}/bestellen', 'Frontend\\SiteController@order', ['throttle:bestellen,10,60']);
    $router->get('/extensies/{slug}', 'Frontend\\SiteController@product');
    $router->get('/extensies/{slug}/bestellen', 'Frontend\\SiteController@checkout');
    $router->post('/extensies/{slug}/bestellen', 'Frontend\\SiteController@order', ['throttle:bestellen,10,60']);
    $router->get('/oplossingen/{slug}', 'Frontend\\SiteController@page');
    $router->get('/domeinen', 'Frontend\\DomainController@search', ['throttle:domein,30,60']);
    $router->get('/domeinen/check', 'Frontend\\DomainController@check', ['throttle:domeincheck,240,60']);
    $router->post('/domeinen/mand', 'Frontend\\DomainController@add', ['throttle:domeinmand,60,60']);
    $router->post('/domeinen/mand/verwijderen', 'Frontend\\DomainController@remove');
    $router->get('/domeinen/winkelwagen', 'Frontend\\DomainController@basket');
    $router->post('/domeinen/winkelwagen', 'Frontend\\DomainController@saveBasket');
    $router->get('/domeinen/extras', 'Frontend\\DomainController@extras');
    $router->post('/domeinen/extras', 'Frontend\\DomainController@saveExtras');
    $router->get('/domeinen/gegevens', 'Frontend\\DomainController@details');
    $router->post('/domeinen/gegevens', 'Frontend\\DomainController@saveDetails', ['throttle:registreren,10,60']);
    $router->get('/domeinen/overzicht', 'Frontend\\DomainController@overview');
    $router->post('/domeinen/bestellen', 'Frontend\\DomainController@order', ['throttle:bestellen,10,60']);
    $router->get('/blog', 'Frontend\\SiteController@blog');
    $router->get('/blog/{slug}', 'Frontend\\SiteController@blogPost');
    $router->get('/portfolio', 'Frontend\\SiteController@portfolio');
    $router->get('/portfolio/{slug}', 'Frontend\\SiteController@portfolioItem');
    $router->get('/kennisbank', 'Frontend\\SiteController@knowledge');
    $router->get('/kennisbank/{slug}', 'Frontend\\SiteController@article');
    $router->get('/over-ons', 'Frontend\\SiteController@about');
    $router->get('/contact', 'Frontend\\SiteController@contact');
    $router->post('/contact', 'Frontend\\SiteController@sendContact', ['throttle:contact,5,60']);
    $router->get('/cron/{key}', 'Frontend\\SiteController@cron');
});

// Inloggen / registreren / wachtwoord
$router->group('', ['csrf', 'guest'], static function ($router): void {
    $router->get('/login', 'Auth\\AuthController@loginForm');
    $router->post('/login', 'Auth\\AuthController@login', ['throttle:login,10,15']);
    $router->get('/registreren', 'Auth\\AuthController@registerForm');
    $router->post('/registreren', 'Auth\\AuthController@register', ['throttle:registreren,5,60']);
    $router->get('/wachtwoord-vergeten', 'Auth\\AuthController@forgotForm');
    $router->post('/wachtwoord-vergeten', 'Auth\\AuthController@forgot', ['throttle:wachtwoord,5,60']);
    $router->get('/wachtwoord-herstellen/{token}', 'Auth\\AuthController@resetForm');
    $router->post('/wachtwoord-herstellen/{token}', 'Auth\\AuthController@reset');
});
$router->post('/uitloggen', 'Auth\\AuthController@logout', ['csrf']);

// Klantportaal
$router->group('klant', ['csrf', 'auth', 'klant'], static function ($router): void {
    $router->get('/', 'Klant\\PortalController@dashboard');
    $router->get('/diensten', 'Klant\\PortalController@services');
    $router->get('/diensten/{id}', 'Klant\\PortalController@service');
    $router->post('/diensten/{id}/paneel', 'Klant\\PortalController@serviceSso');
    $router->post('/diensten/{id}/vps/{actie}', 'Klant\\PortalController@serviceVps');
    $router->post('/diensten/{id}/domein/{actie}', 'Klant\\PortalController@serviceDomain');
    $router->get('/diensten/{id}/hosting', 'Klant\\PortalController@serviceHostingPage');
    $router->post('/diensten/{id}/hosting/{actie}', 'Klant\\PortalController@serviceHosting');
    $router->get('/tegoed', 'Klant\\PortalController@wallet');
    $router->post('/tegoed', 'Klant\\PortalController@topup');
    $router->get('/facturen', 'Klant\\PortalController@invoices');
    $router->get('/offertes', 'Klant\\PortalController@quotes');
    $router->get('/documenten/{id}', 'Klant\\PortalController@document');
    $router->get('/documenten/{id}/pdf', 'Klant\\PortalController@pdf');
    $router->post('/documenten/{id}/betalen', 'Klant\\PortalController@pay');
    $router->post('/offertes/{id}/akkoord', 'Klant\\PortalController@acceptQuote');
    $router->post('/offertes/{id}/afwijzen', 'Klant\\PortalController@rejectQuote');
    $router->get('/tickets', 'Klant\\PortalController@tickets');
    $router->get('/tickets/nieuw', 'Klant\\PortalController@newTicket');
    $router->post('/tickets', 'Klant\\PortalController@storeTicket');
    $router->get('/tickets/{id}', 'Klant\\PortalController@ticket');
    $router->post('/tickets/{id}', 'Klant\\PortalController@replyTicket');
    $router->get('/profiel', 'Klant\\PortalController@profile');
    $router->post('/profiel', 'Klant\\PortalController@updateProfile');
});
$router->post('/klant/terug', 'Beheer\\CustomerController@stopImpersonating', ['csrf', 'auth']);

// Beheer
$router->group('beheer', ['csrf', 'auth', 'staff'], static function ($router): void {
    $router->get('/', 'Beheer\\DashboardController@index');

    $router->get('/klanten', 'Beheer\\CustomerController@index');
    $router->get('/klanten/nieuw', 'Beheer\\CustomerController@create');
    $router->post('/klanten', 'Beheer\\CustomerController@store');
    $router->get('/klanten/{id}', 'Beheer\\CustomerController@show');
    $router->post('/klanten/{id}', 'Beheer\\CustomerController@update');
    $router->delete('/klanten/{id}', 'Beheer\\CustomerController@destroy', ['admin']);
    $router->post('/klanten/{id}/inloggen', 'Beheer\\CustomerController@impersonate');

    $router->get('/diensten', 'Beheer\\ServiceController@index');
    $router->get('/diensten/nieuw', 'Beheer\\ServiceController@create');
    $router->post('/diensten', 'Beheer\\ServiceController@store');
    foreach (\App\Models\Product::SOORTEN as $soorten) {
        foreach (array_keys($soorten) as $soort) {
            $router->get('/diensten/' . \App\Models\Product::soortSlug($soort), 'Beheer\\ServiceController@index');
        }
    }
    $router->get('/diensten/{id}', 'Beheer\\ServiceController@edit');
    $router->post('/diensten/{id}', 'Beheer\\ServiceController@update');
    $router->delete('/diensten/{id}', 'Beheer\\ServiceController@destroy');
    $router->post('/diensten/{id}/factureren', 'Beheer\\ServiceController@invoiceNow');
    $router->post('/diensten/{id}/domein/{actie}', 'Beheer\\ServiceController@domainAction');
    $router->get('/diensten/{id}/hosting', 'Beheer\\ServiceController@hosting');
    $router->post('/diensten/{id}/hosting/{actie}', 'Beheer\\ServiceController@hostingAction');
    $router->post('/diensten/{id}/paneel', 'Beheer\\ServiceController@sso');

    foreach (['facturen' => 'factuur', 'offertes' => 'offerte'] as $plural => $kind) {
        $router->get('/' . $plural, static fn ($r) => (new \App\Controllers\Beheer\DocumentController())->index($r, $kind));
        $router->get('/' . $plural . '/nieuw', static fn ($r) => (new \App\Controllers\Beheer\DocumentController())->create($r, $kind));
        $router->post('/' . $plural, static fn ($r) => (new \App\Controllers\Beheer\DocumentController())->store($r, $kind));
    }
    $router->get('/documenten/{id}', 'Beheer\\DocumentController@show');
    $router->get('/documenten/{id}/bewerken', 'Beheer\\DocumentController@edit');
    $router->post('/documenten/{id}', 'Beheer\\DocumentController@update');
    $router->delete('/documenten/{id}', 'Beheer\\DocumentController@destroy');
    $router->get('/documenten/{id}/pdf', 'Beheer\\DocumentController@pdf');
    $router->post('/documenten/{id}/versturen', 'Beheer\\DocumentController@send');
    $router->post('/documenten/{id}/status', 'Beheer\\DocumentController@status');
    $router->post('/documenten/{id}/betaling', 'Beheer\\DocumentController@payment');
    $router->post('/documenten/{id}/naar-factuur', 'Beheer\\DocumentController@toInvoice');
    $router->post('/documenten/{id}/crediteren', 'Beheer\\DocumentController@credit');
    $router->post('/documenten/{id}/kopie', 'Beheer\\DocumentController@duplicate');

    $router->get('/tickets', 'Beheer\\TicketController@index');
    $router->get('/tickets/nieuw', 'Beheer\\TicketController@create');
    $router->post('/tickets', 'Beheer\\TicketController@store');
    $router->get('/tickets/{id}', 'Beheer\\TicketController@show');
    $router->post('/tickets/{id}', 'Beheer\\TicketController@reply');
    $router->post('/tickets/{id}/status', 'Beheer\\TicketController@status');
    $router->delete('/tickets/{id}', 'Beheer\\TicketController@destroy');

    $router->get('/projecten', 'Beheer\\ContentController@index');
    $router->get('/projecten/nieuw', 'Beheer\\ContentController@create');
    $router->post('/projecten', 'Beheer\\ContentController@store');
    $router->get('/projecten/{id}/taken', 'Beheer\\ProjectController@show');
    $router->post('/projecten/{id}/taken', 'Beheer\\ProjectController@storeTask');
    $router->post('/projecten/{id}/taken/{taak}', 'Beheer\\ProjectController@updateTask');
    $router->delete('/projecten/{id}/taken/{taak}', 'Beheer\\ProjectController@destroyTask');
    $router->get('/projecten/{id}', 'Beheer\\ContentController@edit');
    $router->post('/projecten/{id}', 'Beheer\\ContentController@update');
    $router->delete('/projecten/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/afspraken', 'Beheer\\ContentController@index');
    $router->get('/afspraken/nieuw', 'Beheer\\ContentController@create');
    $router->post('/afspraken', 'Beheer\\ContentController@store');
    $router->get('/afspraken/{id}', 'Beheer\\ContentController@edit');
    $router->post('/afspraken/{id}', 'Beheer\\ContentController@update');
    $router->delete('/afspraken/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/btw', 'Beheer\\VatController@index');
    $router->get('/contracten', 'Beheer\\ContentController@index');
    $router->get('/contracten/nieuw', 'Beheer\\ContentController@create');
    $router->post('/contracten', 'Beheer\\ContentController@store');
    $router->get('/contracten/{id}', 'Beheer\\ContentController@edit');
    $router->post('/contracten/{id}', 'Beheer\\ContentController@update');
    $router->delete('/contracten/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/crediteuren', 'Beheer\\ContentController@index');
    $router->get('/crediteuren/nieuw', 'Beheer\\ContentController@create');
    $router->post('/crediteuren', 'Beheer\\ContentController@store');
    $router->get('/crediteuren/{id}', 'Beheer\\ContentController@edit');
    $router->post('/crediteuren/{id}', 'Beheer\\ContentController@update');
    $router->delete('/crediteuren/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/leads', 'Beheer\\ContentController@index');
    $router->get('/leads/nieuw', 'Beheer\\ContentController@create');
    $router->post('/leads', 'Beheer\\ContentController@store');
    $router->get('/leads/{id}', 'Beheer\\ContentController@edit');
    $router->post('/leads/{id}', 'Beheer\\ContentController@update');
    $router->delete('/leads/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/inkoopfacturen', 'Beheer\\ContentController@index');
    $router->get('/inkoopfacturen/nieuw', 'Beheer\\ContentController@create');
    $router->post('/inkoopfacturen', 'Beheer\\ContentController@store');
    $router->get('/inkoopfacturen/{id}', 'Beheer\\ContentController@edit');
    $router->post('/inkoopfacturen/{id}', 'Beheer\\ContentController@update');
    $router->delete('/inkoopfacturen/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/storingen', 'Beheer\\ContentController@index');
    $router->get('/storingen/nieuw', 'Beheer\\ContentController@create');
    $router->post('/storingen', 'Beheer\\ContentController@store');
    $router->get('/storingen/{id}', 'Beheer\\ContentController@edit');
    $router->post('/storingen/{id}', 'Beheer\\ContentController@update');
    $router->delete('/storingen/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/snelle-antwoorden', 'Beheer\\ContentController@index');
    $router->get('/snelle-antwoorden/nieuw', 'Beheer\\ContentController@create');
    $router->post('/snelle-antwoorden', 'Beheer\\ContentController@store');
    $router->get('/snelle-antwoorden/{id}', 'Beheer\\ContentController@edit');
    $router->post('/snelle-antwoorden/{id}', 'Beheer\\ContentController@update');
    $router->delete('/snelle-antwoorden/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/website/{type}', 'Beheer\\ContentController@index');
    $router->get('/website/{type}/nieuw', 'Beheer\\ContentController@create');
    $router->post('/website/{type}', 'Beheer\\ContentController@store');
    $router->get('/website/{type}/{id}', 'Beheer\\ContentController@edit');
    $router->post('/website/{type}/{id}', 'Beheer\\ContentController@update');
    $router->delete('/website/{type}/{id}', 'Beheer\\ContentController@destroy');

    $router->get('/producten', 'Beheer\\ProductController@index');
    $router->get('/producten/nieuw', 'Beheer\\ProductController@create');
    $router->post('/producten', 'Beheer\\ProductController@store');
    foreach (\App\Models\Product::SOORTEN as $soorten) {
        foreach (array_keys($soorten) as $soort) {
            $router->get('/producten/' . \App\Models\Product::soortSlug($soort, true), 'Beheer\\ProductController@index');
        }
    }
    $router->get('/producten/{id}', 'Beheer\\ProductController@edit');
    $router->post('/producten/{id}', 'Beheer\\ProductController@update');
    $router->delete('/producten/{id}', 'Beheer\\ProductController@destroy');
});

$router->group('beheer', ['csrf', 'auth', 'admin'], static function ($router): void {
    $router->get('/medewerkers', 'Beheer\\StaffController@index');
    $router->get('/medewerkers/nieuw', 'Beheer\\StaffController@create');
    $router->post('/medewerkers', 'Beheer\\StaffController@store');
    $router->get('/medewerkers/{id}', 'Beheer\\StaffController@edit');
    $router->post('/medewerkers/{id}', 'Beheer\\StaffController@update');
    $router->delete('/medewerkers/{id}', 'Beheer\\StaffController@destroy');

    $router->get('/instellingen', 'Beheer\\SettingsController@index');
    $router->post('/instellingen', 'Beheer\\SettingsController@update');
    $router->post('/instellingen/branding', 'Beheer\\SettingsController@branding');
    $router->post('/instellingen/branding/verwijderen', 'Beheer\\SettingsController@removeBranding');
    $router->post('/instellingen/testmail', 'Beheer\\SettingsController@testMail');

    $router->get('/updates', 'Beheer\\UpdateController@index');
    $router->post('/updates', 'Beheer\\UpdateController@save');
    $router->post('/updates/installeren', 'Beheer\\UpdateController@install');
    $router->post('/updates/migraties', 'Beheer\\UpdateController@migrate');

    $router->get('/demo-data', 'Beheer\\DemoController@index');
    $router->post('/demo-data', 'Beheer\\DemoController@destroy');

    $router->get('/koppelingen', 'Beheer\\IntegrationController@index');
    $router->get('/koppelingen/realtimeregister/domeinen', 'Beheer\\IntegrationController@rrDomains');
    $router->post('/koppelingen/realtimeregister/domeinen', 'Beheer\\IntegrationController@rrImportDomains');
    $router->get('/koppelingen/realtimeregister/extensies', 'Beheer\\IntegrationController@rrTlds');
    $router->post('/koppelingen/realtimeregister/extensies', 'Beheer\\IntegrationController@rrImportTlds');
    $router->get('/koppelingen/qevanto/diensten', 'Beheer\\IntegrationController@qevanto');
    $router->post('/koppelingen/qevanto/diensten', 'Beheer\\IntegrationController@qevantoImport');
    $router->get('/koppelingen/qevanto/pakketten', 'Beheer\\IntegrationController@qevantoPackages');
    $router->post('/koppelingen/qevanto/pakketten', 'Beheer\\IntegrationController@qevantoImportPackages');
    $router->get('/koppelingen/{naam}', 'Beheer\\IntegrationController@show');
    $router->post('/koppelingen/{naam}', 'Beheer\\IntegrationController@save');
    $router->post('/koppelingen/{naam}/test', 'Beheer\\IntegrationController@test');
});

// Betaal-webhooks (zonder CSRF; verificatie bij de provider of via handtekening)
$router->post('/betalen/mollie/webhook', 'Frontend\\WebhookController@mollie');
$router->post('/betalen/cryptomus/webhook', 'Frontend\\WebhookController@cryptomus');
