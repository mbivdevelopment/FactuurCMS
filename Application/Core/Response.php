<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    /** @param array<string, string> $headers */
    public function __construct(
        private string $content = '',
        private int $status = 200,
        private array $headers = [],
    ) {
    }

    public static function html(string $content, int $status = 200): self
    {
        return new self($content, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /** @param array<string, mixed> $data */
    public static function view(string $view, array $data = [], int $status = 200): self
    {
        return self::html(View::render($view, $data), $status);
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
            $status,
            ['Content-Type' => 'application/json; charset=UTF-8'],
        );
    }

    public static function redirect(string $to, int $status = 302): self
    {
        $base = base_path_url();
        if ($base !== '' && str_starts_with($to, '/') && !str_starts_with($to, '//') && !str_starts_with($to, $base . '/')) {
            $to = $base . $to;
        }

        return new self('', $status, ['Location' => $to]);
    }

    public static function back(string $fallback = '/'): self
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = strtolower((string) parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST));
        $to = ($referer !== '' && strtolower((string) parse_url($referer, PHP_URL_HOST)) === $host) ? $referer : $fallback;

        return self::redirect($to);
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function with(string $key, mixed $value): self
    {
        Session::flash($key, $value);

        return $this;
    }

    /** @param array<string, string> $errors */
    public function withErrors(array $errors): self
    {
        Session::flash('errors', $errors);

        return $this;
    }

    /** @param array<string, mixed> $input */
    public function withInput(array $input): self
    {
        unset($input['password'], $input['password_confirmation'], $input['_token']);
        Session::flash('old', $input);

        return $this;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: same-origin');
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        echo $this->content;
    }
}
