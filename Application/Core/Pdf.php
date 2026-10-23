<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimale PDF-writer (geen externe library): A4, Helvetica (standaardfonts, WinAnsi), tekst met uitlijning
 * en regelafbreking, rechthoeken, lijnen en afbeeldingen (via GD als JPEG). Maten in mm.
 */
final class Pdf
{
    private const W = 210.0;
    private const H = 297.0;
    private const K = 72 / 25.4;
    private const WIDTHS = [
        'R' => [278,278,355,556,556,889,667,191,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,278,278,584,584,584,556,1015,667,667,722,722,667,611,778,722,278,500,667,556,833,722,778,667,778,722,667,611,722,667,944,667,667,611,278,278,278,469,556,333,556,556,500,556,556,278,556,556,222,222,500,222,833,556,556,556,556,333,500,278,556,500,722,500,500,500,334,260,334,584,350,556,350,222,556,333,1000,556,556,333,1000,667,333,1000,350,611,350,350,222,222,333,333,350,556,1000,333,1000,500,333,944,350,500,667,278,333,556,556,556,556,260,556,333,737,370,556,584,333,737,333,400,584,333,333,333,556,537,278,333,333,365,556,834,834,834,611,667,667,667,667,667,667,1000,722,667,667,667,667,278,278,278,278,722,722,778,778,778,778,778,584,778,722,722,722,722,667,667,611,556,556,556,556,556,556,889,500,556,556,556,556,278,278,278,278,556,556,556,556,556,556,556,584,611,556,556,556,556,500,556,500],
        'B' => [278,333,474,556,556,889,722,238,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,333,333,584,584,584,611,975,722,722,722,722,667,611,778,722,278,556,722,611,833,722,778,667,778,722,667,611,722,667,944,667,667,611,333,278,333,584,556,333,556,611,556,611,556,333,611,611,278,278,556,278,889,611,611,611,611,389,556,333,611,556,778,556,556,500,389,280,389,584,350,556,350,278,556,500,1000,556,556,333,1000,667,333,1000,350,611,350,350,278,278,500,500,350,556,1000,333,1000,556,333,944,350,500,667,278,333,556,556,556,556,280,556,333,737,370,556,584,333,737,333,400,584,333,333,333,611,556,278,333,333,365,556,834,834,834,611,722,722,722,722,722,722,1000,722,667,667,667,667,278,278,278,278,722,722,778,778,778,778,778,584,778,722,722,722,722,667,667,611,556,556,556,556,556,556,889,556,556,556,556,556,278,278,278,278,611,611,611,611,611,611,611,584,611,611,611,611,611,556,611,556],
    ];

    /** @var list<string> */
    private array $pages = [];
    private string $buf = '';
    /** @var array<string, array{data: string, w: int, h: int, n: int}> */
    private array $images = [];
    private string $font = 'R';
    private float $size = 10;
    private string $textColor = '0 g';
    public float $y = 0;
    public float $marginTop = 15;
    public float $marginBottom = 20;
    /** @var callable|null */
    private $header = null;
    /** @var callable|null */
    private $footer = null;

    public function __construct(public readonly string $title = '')
    {
    }

    public function onPage(?callable $header, ?callable $footer = null): void
    {
        $this->header = $header;
        $this->footer = $footer;
    }

    public function addPage(): void
    {
        if ($this->buf !== '') {
            $this->closePage();
        }
        $this->buf = ' ';
        $this->y = $this->marginTop;
        if ($this->header !== null) {
            ($this->header)($this);
        }
    }

    private function closePage(): void
    {
        if ($this->footer !== null) {
            ($this->footer)($this, count($this->pages) + 1);
        }
        $this->pages[] = $this->buf;
        $this->buf = '';
    }

    public function pageCount(): int
    {
        return count($this->pages) + ($this->buf !== '' ? 1 : 0);
    }

    /** Nieuwe pagina als er minder dan $need mm over is. */
    public function ensure(float $need): void
    {
        if ($this->y + $need > self::H - $this->marginBottom) {
            $this->addPage();
        }
    }

    public function font(string $style = 'R', float $size = 10): void
    {
        $this->font = $style === 'B' ? 'B' : 'R';
        $this->size = $size;
    }

    public function textColor(string $hex): void
    {
        $this->textColor = self::rgb($hex);
    }

    private static function rgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            $hex = '000000';
        }

        return sprintf('%.3F %.3F %.3F rg', hexdec(substr($hex, 0, 2)) / 255, hexdec(substr($hex, 2, 2)) / 255, hexdec(substr($hex, 4, 2)) / 255);
    }

    public function rect(float $x, float $y, float $w, float $h, string $hex): void
    {
        $this->buf .= sprintf("%s %.2F %.2F %.2F %.2F re f\n", self::rgb($hex), $x * self::K, (self::H - $y - $h) * self::K, $w * self::K, $h * self::K);
    }

    public function line(float $x1, float $y1, float $x2, float $y2, string $hex, float $width = 0.2): void
    {
        $this->buf .= sprintf("%s %.2F w %.2F %.2F m %.2F %.2F l S\n", str_replace(' rg', ' RG', self::rgb($hex)), $width * self::K, $x1 * self::K, (self::H - $y1) * self::K, $x2 * self::K, (self::H - $y2) * self::K);
    }

    public function width(string $text): float
    {
        $t = self::enc($text);
        $w = 0;
        $table = self::WIDTHS[$this->font];
        for ($i = 0, $n = strlen($t); $i < $n; $i++) {
            $c = ord($t[$i]);
            $w += $c >= 32 ? $table[$c - 32] : 278;
        }

        return $w * $this->size / 1000 / self::K;
    }

    private static function enc(string $text): string
    {
        $out = @iconv('UTF-8', 'WINDOWS-1252//TRANSLIT', $text);

        return $out === false ? preg_replace('/[^\x20-\x7E]/', '?', $text) ?? '' : $out;
    }

    /** Eén regel tekst; align L/R/C binnen breedte $w vanaf $x. */
    public function text(float $x, float $y, string $text, string $align = 'L', float $w = 0): void
    {
        $tw = $this->width($text);
        if ($align === 'R') {
            $x += $w - $tw;
        } elseif ($align === 'C') {
            $x += ($w - $tw) / 2;
        }
        $s = strtr(self::enc($text), ['\\' => '\\\\', '(' => '\\(', ')' => '\\)', "\r" => '']);
        $this->buf .= sprintf("BT %s /F%s %.2F Tf %.2F %.2F Td (%s) Tj ET\n", $this->textColor, $this->font, $this->size, $x * self::K, (self::H - $y) * self::K, $s);
    }

    /** @return list<string> */
    public function wrap(string $text, float $w): array
    {
        $lines = [];
        foreach (preg_split('/\r\n|\n/', $text) ?: [] as $para) {
            $cur = '';
            foreach (explode(' ', $para) as $word) {
                $try = $cur === '' ? $word : "$cur $word";
                if ($cur !== '' && $this->width($try) > $w) {
                    $lines[] = $cur;
                    $cur = $word;
                } else {
                    $cur = $try;
                }
            }
            $lines[] = $cur;
        }

        return $lines;
    }

    /** Meerregelige tekst vanaf ($x, $y); geeft nieuwe y terug. */
    public function paragraph(float $x, float $y, float $w, string $text, float $lineHeight = 0, string $align = 'L'): float
    {
        $lh = $lineHeight > 0 ? $lineHeight : $this->size * 0.45;
        foreach ($this->wrap($text, $w) as $line) {
            $this->text($x, $y + $lh * 0.75, $line, $align, $w);
            $y += $lh;
        }

        return $y;
    }

    /** Afbeelding (jpg/png/gif/webp) via GD, geschaald in box $w x $h (mm), behoudt verhouding. Geeft [w,h] terug. */
    public function image(string $path, float $x, float $y, float $maxW, float $maxH, string $align = 'L'): array
    {
        if (!isset($this->images[$path])) {
            if (!is_file($path) || !function_exists('imagecreatefromstring')) {
                return [0.0, 0.0];
            }
            $src = @imagecreatefromstring((string) file_get_contents($path));
            if ($src === false) {
                return [0.0, 0.0];
            }
            $w = imagesx($src);
            $h = imagesy($src);
            $canvas = imagecreatetruecolor($w, $h);
            if ($canvas === false) {
                return [0.0, 0.0];
            }
            imagefill($canvas, 0, 0, (int) imagecolorallocate($canvas, 255, 255, 255));
            imagecopy($canvas, $src, 0, 0, 0, 0, $w, $h);
            ob_start();
            imagejpeg($canvas, null, 90);
            $data = (string) ob_get_clean();
            $this->images[$path] = ['data' => $data, 'w' => $w, 'h' => $h, 'n' => count($this->images) + 1];
        }
        $img = $this->images[$path];
        $ratio = min($maxW / $img['w'], $maxH / $img['h']);
        $dw = $img['w'] * $ratio;
        $dh = $img['h'] * $ratio;
        if ($align === 'R') {
            $x += $maxW - $dw;
        } elseif ($align === 'C') {
            $x += ($maxW - $dw) / 2;
        }
        $this->buf .= sprintf("q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q\n", $dw * self::K, $dh * self::K, $x * self::K, (self::H - $y - $dh) * self::K, $img['n']);

        return [$dw, $dh];
    }

    public function output(): string
    {
        if ($this->buf !== '') {
            $this->closePage();
        }
        $objects = [];
        $add = static function (string $content) use (&$objects): int {
            $objects[] = $content;

            return count($objects);
        };
        $fontR = $add("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
        $fontB = $add("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");
        $imgRefs = '';
        foreach ($this->images as $img) {
            $n = $add("<< /Type /XObject /Subtype /Image /Width {$img['w']} /Height {$img['h']} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($img['data']) . " >>\nstream\n" . $img['data'] . "\nendstream");
            $imgRefs .= " /I{$img['n']} $n 0 R";
        }
        $resources = $add("<< /Font << /F" . "R $fontR 0 R /F" . "B $fontB 0 R >> /XObject <<$imgRefs >> >>");
        $pagesIndex = count($objects) + 1;
        $objects[] = '';
        $pageIds = [];
        foreach ($this->pages as $content) {
            $stream = gzcompress($content, 6) ?: $content;
            $c = $add("<< /Length " . strlen($stream) . " /Filter /FlateDecode >>\nstream\n" . $stream . "\nendstream");
            $pageIds[] = $add(sprintf("<< /Type /Page /Parent %d 0 R /MediaBox [0 0 %.2F %.2F] /Resources %d 0 R /Contents %d 0 R >>", $pagesIndex, self::W * self::K, self::H * self::K, $resources, $c));
        }
        $objects[$pagesIndex - 1] = "<< /Type /Pages /Kids [" . implode(' ', array_map(static fn (int $id): string => "$id 0 R", $pageIds)) . "] /Count " . count($pageIds) . " >>";
        $info = $add("<< /Title (" . strtr(self::enc($this->title), ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']) . ") /Producer (FactuurCMS) /CreationDate (D:" . date('YmdHis') . ") >>");
        $catalog = $add("<< /Type /Catalog /Pages $pagesIndex 0 R >>");

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objects as $i => $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n$obj\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $o) {
            $pdf .= sprintf("%010d 00000 n \n", $o);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root $catalog 0 R /Info $info 0 R >>\nstartxref\n$xref\n%%EOF\n";

        return $pdf;
    }
}
