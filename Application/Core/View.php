<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    /** @var array<string, mixed> */
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = []): string
    {
        $file = str_starts_with($view, '/') ? $view : App::instance()->path('Application/Views/' . str_replace('.', '/', $view) . '.php');
        if (!is_file($file)) {
            throw new \RuntimeException("View '$view' niet gevonden.");
        }

        $layout = null;
        $layoutData = [];
        $sections = [];
        $data = array_merge(self::$shared, $data);

        $content = self::capture($file, $data, $layout, $layoutData, $sections);

        if ($layout !== null) {
            $sections['content'] ??= $content;

            return self::render('layouts.' . $layout, $layoutData + $data + ['__sections' => $sections]);
        }

        return $content;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $layoutData
     * @param array<string, string> $sections
     */
    private static function capture(string $file, array $data, ?string &$layout, array &$layoutData, array &$sections): string
    {
        $sectionStack = [];
        $__sections = $data['__sections'] ?? [];

        /** @param array<string, mixed> $extra variabelen voor de layout (bv. title) */
        $extend = static function (string $name, array $extra = []) use (&$layout, &$layoutData): void {
            $layout = $name;
            $layoutData = $extra;
        };
        $section = static function (string $name) use (&$sectionStack): void {
            $sectionStack[] = $name;
            ob_start();
        };
        $endsection = static function () use (&$sectionStack, &$sections): void {
            $name = array_pop($sectionStack);
            $sections[$name] = (string) ob_get_clean();
        };
        $yield = static function (string $name, string $default = '') use (&$sections, $__sections): string {
            return $sections[$name] ?? $__sections[$name] ?? $default;
        };
        $partial = static function (string $view, array $extra = []) use ($data): string {
            return self::render($view, array_merge($data, $extra));
        };

        foreach (['data', 'file', 'layout', 'layoutData', 'sections', 'sectionStack', 'extend', 'section', 'endsection', 'yield', 'partial'] as $reserved) {
            if (array_key_exists($reserved, $data)) {
                throw new \RuntimeException("Viewvariabele '\$$reserved' is gereserveerd.");
            }
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
