<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class App
{
    private static ?App $instance = null;

    public readonly string $basePath;
    public readonly Router $router;

    public function __construct(string $basePath)
    {
        self::$instance = $this;
        $this->basePath = rtrim($basePath, '/');

        Config::load($this->basePath . '/Application/Config');
        date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Amsterdam'));
        mb_internal_encoding('UTF-8');

        error_reporting(E_ALL);
        ini_set('display_errors', Config::get('app.debug') ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', $this->basePath . '/storage/logs/php-error.log');

        $this->router = new Router();
    }

    public static function instance(): App
    {
        if (self::$instance === null) {
            throw new \RuntimeException('App is nog niet geïnitialiseerd.');
        }

        return self::$instance;
    }

    public function path(string $relative = ''): string
    {
        return $this->basePath . ($relative !== '' ? '/' . ltrim($relative, '/') : '');
    }

    public function run(): void
    {
        Session::start();

        foreach (glob($this->path('Application/Routes/*.php')) ?: [] as $file) {
            (static function (Router $router) use ($file): void {
                require $file;
            })($this->router);
        }

        $request = Request::capture();

        try {
            $response = $this->router->dispatch($request);
        } catch (ValidationException $e) {
            $response = $e->toResponse();
        } catch (HttpException $e) {
            $response = $this->errorResponse($request, $e->getCode(), $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Onverwachte fout', ['exception' => (string) $e]);
            $message = Config::get('app.debug') ? $e->getMessage() . "\n" . $e->getTraceAsString() : 'Er is een onverwachte fout opgetreden.';
            $response = $this->errorResponse($request, 500, $message);
        }

        $response->send();
    }

    private function errorResponse(Request $request, int $status, string $message): Response
    {
        if ($request->wantsJson()) {
            return Response::json(['error' => $message], $status);
        }
        if ($status === 419 && !in_array($request->method, ['GET', 'HEAD'], true)) {
            return Response::back('/')->with('error', 'Je sessie was verlopen; probeer het opnieuw.');
        }
        try {
            return Response::view('errors.error', ['status' => $status, 'message' => $message], $status);
        } catch (Throwable) {
            return Response::html('<h1>' . $status . '</h1><p>' . htmlspecialchars($message) . '</p>', $status);
        }
    }
}
