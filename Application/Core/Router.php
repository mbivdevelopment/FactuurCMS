<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

final class Router
{
    /** @var array<int, array{method: string, pattern: string, regex: string, handler: mixed, middleware: string[], name: ?string}> */
    private array $routes = [];

    /** @var array<string, string> */
    private array $names = [];

    /** @var array{prefix: string, middleware: string[]} */
    private array $group = ['prefix' => '', 'middleware' => []];

    /** @param string[] $middleware */
    public function group(string $prefix, array $middleware, Closure $callback): void
    {
        $previous = $this->group;
        $this->group = [
            'prefix' => $previous['prefix'] . '/' . trim($prefix, '/'),
            'middleware' => array_merge($previous['middleware'], $middleware),
        ];
        $callback($this);
        $this->group = $previous;
    }

    /** @param string[] $middleware */
    public function get(string $pattern, mixed $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('GET', $pattern, $handler, $middleware, $name);
    }

    /** @param string[] $middleware */
    public function post(string $pattern, mixed $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('POST', $pattern, $handler, $middleware, $name);
    }

    /** @param string[] $middleware */
    public function put(string $pattern, mixed $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('PUT', $pattern, $handler, $middleware, $name);
    }

    /** @param string[] $middleware */
    public function delete(string $pattern, mixed $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('DELETE', $pattern, $handler, $middleware, $name);
    }

    /** @param string[] $middleware */
    public function add(string $method, string $pattern, mixed $handler, array $middleware = [], ?string $name = null): void
    {
        $full = '/' . trim($this->group['prefix'] . '/' . trim($pattern, '/'), '/');
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $full) . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $full,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => array_merge($this->group['middleware'], $middleware),
            'name' => $name,
        ];

        if ($name !== null) {
            $this->names[$name] = $full;
        }
    }

    /** @param array<string, string|int> $params */
    public function url(string $name, array $params = []): string
    {
        $path = $this->names[$name] ?? throw new \InvalidArgumentException("Route '$name' bestaat niet.");
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', rawurlencode((string) $value), $path);
        }

        return $path;
    }

    public function dispatch(Request $request): Response
    {
        $methodAllowed = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $request->path, $matches)) {
                continue;
            }
            $methodAllowed = true;
            if ($route['method'] !== $request->method && !($route['method'] === 'GET' && $request->method === 'HEAD')) {
                continue;
            }

            $request->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            $pipeline = array_reduce(
                array_reverse($route['middleware']),
                static fn (Closure $next, string $middleware): Closure => static function (Request $request) use ($middleware, $next): Response {
                    return Middleware::resolve($middleware)->handle($request, $next);
                },
                fn (Request $request): Response => $this->call($route['handler'], $request),
            );

            return $pipeline($request);
        }

        throw new HttpException($methodAllowed ? 405 : 404, $methodAllowed ? 'Methode niet toegestaan.' : '');
    }

    private function call(mixed $handler, Request $request): Response
    {
        if ($handler instanceof Closure || (is_array($handler) && is_callable($handler))) {
            $result = $handler($request);
        } elseif (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
            $class = str_starts_with($class, 'App\\') || str_starts_with($class, '\\') ? $class : 'App\\Controllers\\' . $class;
            $controller = new $class();
            $result = $controller->$method($request);
        } else {
            throw new \RuntimeException('Ongeldige route-handler.');
        }

        if ($result instanceof Response) {
            return $result;
        }
        if (is_array($result)) {
            return Response::json($result);
        }

        return Response::html((string) $result);
    }
}
