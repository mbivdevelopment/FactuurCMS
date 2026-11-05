<?php

declare(strict_types=1);

namespace App\Core;

/** Minimale TrueType-parser: metrics + cmap + hmtx voor embedding in Pdf (WinAnsi, zonder externe libs). */
final class TtfFont
{
    private const WINANSI_HIGH = [
        0x80 => 0x20AC, 0x82 => 0x201A, 0x83 => 0x0192, 0x84 => 0x201E, 0x85 => 0x2026,
        0x86 => 0x2020, 0x87 => 0x2021, 0x88 => 0x02C6, 0x89 => 0x2030, 0x8A => 0x0160,
        0x8B => 0x2039, 0x8C => 0x0152, 0x8E => 0x017D, 0x91 => 0x2018, 0x92 => 0x2019,
        0x93 => 0x201C, 0x94 => 0x201D, 0x95 => 0x2022, 0x96 => 0x2013, 0x97 => 0x2014,
        0x98 => 0x02DC, 0x99 => 0x2122, 0x9A => 0x0161, 0x9B => 0x203A, 0x9C => 0x0153,
        0x9E => 0x017E, 0x9F => 0x0178,
    ];

    /** @return array{data:string,name:string,upem:int,bbox:array<int>,ascent:int,descent:int,capHeight:int,italic:int,stemV:int,widths:list<int>} */
    public static function load(string $path): array
    {
        $data = (string) file_get_contents($path);
        $tables = [];
        $numTables = self::u16($data, 4);
        for ($i = 0; $i < $numTables; $i++) {
            $off = 12 + $i * 16;
            $tables[substr($data, $off, 4)] = [self::u32($data, $off + 8), self::u32($data, $off + 12)];
        }
        $t = static function (string $tag) use ($data, $tables): string {
            return substr($data, $tables[$tag][0], $tables[$tag][1]);
        };
        $head = $t('head');
        $upem = self::u16($head, 18);
        $bbox = [self::i16($head, 36), self::i16($head, 38), self::i16($head, 40), self::i16($head, 42)];
        $hhea = $t('hhea');
        $ascent = self::i16($hhea, 4);
        $descent = self::i16($hhea, 6);
        $numMetrics = self::u16($hhea, 34);
        $os2 = $tables['OS/2'] ?? null;
        $capHeight = $ascent;
        $weight = 400;
        if ($os2 !== null) {
            $os2d = substr($data, $os2[0], $os2[1]);
            $weight = self::u16($os2d, 4);
            if (strlen($os2d) >= 90) {
                $capHeight = self::i16($os2d, 88);
            }
        }
        $italic = (int) round(self::i32fixed($t('post'), 4));
        $stemV = (int) max(60, round(40 + $weight / 8));
        $name = '';
        if (isset($tables['name'])) {
            $name = self::psName($t('name')) ?: 'EmbeddedFont';
        }

        // hmtx -> gid => advance width
        $numGlyphs = self::u16($t('maxp'), 4);
        $hmtx = $t('hmtx');
        $gidW = [];
        for ($g = 0; $g < $numGlyphs; $g++) {
            $gidW[$g] = self::u16($hmtx, min($g, $numMetrics - 1) * 4);
        }

        // cmap format 4 -> unicode => gid
        $cmap = [];
        foreach (self::subtables($t('cmap')) as $off) {
            if (self::u16($t('cmap'), $off) === 4) {
                $cmap = self::fmt4($t('cmap'), $off);
                break;
            }
        }

        // WinAnsi-breedtes voor chars 32..255
        $widths = [];
        for ($c = 32; $c <= 255; $c++) {
            $u = self::WINANSI_HIGH[$c] ?? $c;
            $gid = $cmap[$u] ?? 0;
            $widths[] = (int) round(($gidW[$gid] ?? $gidW[0] ?? 500) * 1000 / $upem);
        }

        return ['data' => $data, 'name' => $name, 'upem' => $upem, 'bbox' => $bbox, 'ascent' => $ascent, 'descent' => $descent, 'capHeight' => $capHeight, 'italic' => $italic, 'stemV' => $stemV, 'widths' => $widths];
    }

    private static function u16(string $d, int $o): int
    {
        return (ord($d[$o]) << 8) | ord($d[$o + 1]);
    }

    private static function i16(string $d, int $o): int
    {
        $v = self::u16($d, $o);

        return $v > 32767 ? $v - 65536 : $v;
    }

    private static function u32(string $d, int $o): int
    {
        return (ord($d[$o]) << 24) | (ord($d[$o + 1]) << 16) | (ord($d[$o + 2]) << 8) | ord($d[$o + 3]);
    }

    private static function i32fixed(string $d, int $o): float
    {
        $v = self::u32($d, $o);
        if ($v > 0x7FFFFFFF) {
            $v -= 0x100000000;
        }

        return $v / 65536;
    }

    /** @return list<int> offsets van cmap-subtables */
    private static function subtables(string $cmap): array
    {
        $out = [];
        $n = self::u16($cmap, 2);
        $picked = null;
        for ($i = 0; $i < $n; $i++) {
            $pid = self::u16($cmap, 4 + $i * 8);
            $eid = self::u16($cmap, 6 + $i * 8);
            $off = self::u32($cmap, 8 + $i * 8);
            $out[] = $off;
            if ($pid === 3 && in_array($eid, [1, 10], true)) {
                $picked = $off;
            }
        }

        return $picked !== null ? [$picked] : $out;
    }

    /** @return array<int,int> unicode => gid uit een cmap format 4-subtable */
    private static function fmt4(string $cmap, int $o): array
    {
        $segCount = self::u16($cmap, $o + 6) / 2;
        $endOff = $o + 14;
        $startOff = $endOff + 2 * $segCount + 2;
        $deltaOff = $startOff + 2 * $segCount;
        $roffOff = $deltaOff + 2 * $segCount;
        $map = [];
        for ($s = 0; $s < $segCount; $s++) {
            $end = self::u16($cmap, $endOff + 2 * $s);
            $start = self::u16($cmap, $startOff + 2 * $s);
            $delta = self::i16($cmap, $deltaOff + 2 * $s);
            $roff = self::u16($cmap, $roffOff + 2 * $s);
            for ($c = $start; $c <= $end && $c < 0x10000; $c++) {
                if ($roff === 0) {
                    $map[$c] = ($c + $delta) & 0xFFFF;
                } else {
                    $gidOff = $roffOff + 2 * $s + $roff + 2 * ($c - $start);
                    $g = self::u16($cmap, $gidOff);
                    $map[$c] = $g === 0 ? 0 : ($g + $delta) & 0xFFFF;
                }
            }
        }

        return $map;
    }

    private static function psName(string $name): string
    {
        $count = self::u16($name, 2);
        $so = self::u16($name, 4);
        $best = '';
        for ($i = 0; $i < $count; $i++) {
            $off = 6 + $i * 12;
            $nid = self::u16($name, $off + 6);
            if ($nid !== 6) {
                continue;
            }
            $len = self::u16($name, $off + 8);
            $str = substr($name, $so + self::u16($name, $off + 10), $len);
            if (self::u16($name, $off) === 3) {
                $str = mb_convert_encoding($str, 'UTF-8', 'UTF-16BE');
            }
            $best = $str;
            if (self::u16($name, $off) === 3) {
                break;
            }
        }

        return preg_replace('/[^A-Za-z0-9]/', '', $best) ?? '';
    }
}
