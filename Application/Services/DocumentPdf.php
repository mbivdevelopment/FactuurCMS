<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Pdf;
use App\Models\Document;

/** Factuur-/offerte-PDF in huisstijl: kopband, adresblokken, metabox, regeltabel, totalen, betaalinfo (A4, mm). */
final class DocumentPdf
{
    public static function render(Document $doc): string
    {
        $customer = $doc->customer();
        $primary = (string) setting('branding.kleur', '#ff5a1f');
        $label = $doc->label();
        $pdf = new Pdf($label . ' ' . $doc->Nummer);
        $moeder = trim((string) setting('bedrijf.moeder', ''));
        $handelsnaam = $moeder !== '' ? company_name() . ' is een handelsnaam van ' . $moeder . '.' : null;
        $footer = static function (Pdf $pdf) use ($handelsnaam, $primary): void {
            $pdf->line(20, 266, 190, 266, '#e5e7eb');
            $pdf->rect(20, 266, 170, 1.2, $primary);
            $cols = [
                ['BEDRIJF', array_filter([
                    company_name(),
                    $handelsnaam,
                    setting('bedrijf.adres'),
                    trim(setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', '')),
                ])],
                ['CONTACT', array_filter([setting('bedrijf.email'), setting('bedrijf.telefoon'), setting('bedrijf.website')])],
                ['BANK & REGISTRATIE', array_filter([
                    setting('bedrijf.iban') ? 'IBAN ' . setting('bedrijf.iban') : null,
                    setting('bedrijf.kvk') ? 'KvK ' . setting('bedrijf.kvk') : null,
                    setting('bedrijf.btw') ? 'Btw ' . setting('bedrijf.btw') : null,
                ])],
            ];
            $x = 20;
            foreach ($cols as [$head, $lines]) {
                $pdf->font('B', 7.5);
                $pdf->textColor($primary);
                $pdf->text($x, 271, $head);
                $pdf->font('R', 8);
                $pdf->textColor('#555555');
                $fy = 275.5;
                foreach ($lines as $fl) {
                    $pdf->text($x, $fy, (string) $fl);
                    $fy += 4.4;
                }
                $x += 58;
            }
        };
        $pdf->onPage(null, $footer);
        $pdf->addPage();

        // Kop: primaire band; links logo + bedrijfsgegevens, rechts documenttitel.
        $pdf->rect(0, 0, 210, 6, $primary);
        $logo = branding_path('logo');
        $logoOk = false;
        if ($logo !== '' && !str_ends_with(strtolower($logo), '.svg')) {
            try {
                $pdf->image($logo, 20, 14, 58, 18);
                $logoOk = true;
            } catch (\Throwable) {
                $logoOk = false;
            }
        }
        if (!$logoOk) {
            $pdf->font('B', 19);
            $pdf->textColor($primary);
            $pdf->text(20, 26, company_name());
        }
        $pdf->font('B', 24);
        $pdf->textColor($primary);
        $pdf->text(190, 25, mb_strtoupper($label), 'R');
        $pdf->font('R', 9);
        $pdf->textColor('#888888');
        $pdf->text(190, 31.5, (string) $doc->Nummer, 'R');

        // Adresblok klant (links) + metabox (rechts), onder de kop.
        $y = 36;
        $pdf->line(20, $y + 2, 190, $y + 2, '#e5e7eb');
        $boxTop = $y + 6;
        $custLines = array_filter([
            $customer?->Bedrijfsnaam ? $customer->name() : null,
            $customer?->Adres,
            trim(($customer?->Postcode ?? '') . ' ' . ($customer?->Plaats ?? '') . ($customer?->Land && !in_array($customer->Land, ['NL', 'Nederland'], true) ? ' · ' . $customer->Land : '')),
            $customer?->KvkNummer ? 'KvK: ' . $customer->KvkNummer : null,
            $customer?->BtwNummer ? 'Btw: ' . $customer->BtwNummer : null,
        ]);
        $boxH = 11 + 4.6 * count($custLines) + 3;
        $meta = array_filter([
            [$label . 'nummer', (string) $doc->Nummer],
            [$doc->isQuote() ? 'Offertedatum' : 'Factuurdatum', nl_date((string) $doc->Datum)],
            [$doc->isQuote() ? 'Geldig tot' : 'Vervaldatum', nl_date((string) $doc->VervalDatum)],
            ['Klantnummer', (string) ($customer?->Nummer ?? '')],
            $doc->isQuote() || $doc->Status === 'betaald' ? null : ['Te betalen', money($doc->outstanding()), true],
        ]);
        $metaH = 6 + 6.5 * count($meta);
        $boxH = max($boxH, $metaH);
        $pdf->rect(20, $boxTop, 85, $boxH, '#f6f7f8');
        $pdf->rect(20, $boxTop, 2, $boxH, $primary);
        $pdf->font('R', 7);
        $pdf->textColor('#888888');
        $pdf->text(26, $boxTop + 5, 'FACTUURADRES');
        $pdf->font('B', 10);
        $pdf->textColor('#111111');
        $pdf->text(26, $boxTop + 10.5, (string) $customer?->display());
        $pdf->font('R', 9);
        $cy = $boxTop + 15.5;
        foreach ($custLines as $row) {
            $pdf->text(26, $cy, (string) $row);
            $cy += 4.6;
        }

        $pdf->rect(112, $boxTop, 78, $boxH, '#ffffff');
        foreach ([[112, $boxTop, 190, $boxTop], [112, $boxTop + $boxH, 190, $boxTop + $boxH], [112, $boxTop, 112, $boxTop + $boxH], [190, $boxTop, 190, $boxTop + $boxH]] as [$x1, $y1, $x2, $y2]) {
            $pdf->line($x1, $y1, $x2, $y2, '#e5e7eb');
        }
        $my = $boxTop + ($boxH - 6.5 * count($meta)) / 2 + 4;
        foreach ($meta as $row) {
            [$k, $v] = $row;
            $accent = $row[2] ?? false;
            $pdf->font('R', 7.5);
            $pdf->textColor('#888888');
            $pdf->text(115, $my, mb_strtoupper((string) $k));
            $pdf->font('B', 9);
            $pdf->textColor($accent ? $primary : '#111111');
            $pdf->text(187, $my, (string) $v, 'R');
            $my += 6.5;
        }

        $y = $boxTop + $boxH + 8;
        if ((string) $doc->Onderwerp !== '') {
            $pdf->font('B', 10);
            $pdf->textColor('#111111');
            $pdf->text(20, $y, 'Betreft: ' . $doc->Onderwerp);
            $y += 7;
        }

        // Regeltabel.
        $pdf->rect(20, $y - 4.5, 170, 8, $primary);
        $pdf->font('B', 8.5);
        $pdf->textColor('#ffffff');
        $pdf->text(22, $y, 'Omschrijving');
        $pdf->text(122, $y, 'Aantal', 'R');
        $pdf->text(148, $y, 'Prijs', 'R');
        $pdf->text(162, $y, 'Btw', 'R');
        $pdf->text(188, $y, 'Totaal', 'R');
        $pdf->font('R', 9);
        $pdf->textColor('#111111');
        $y += 8;
        $stripe = false;
        foreach ($doc->lines() as $line) {
            $rows = $pdf->wrap((string) $line['Omschrijving'], 92);
            $sub = is_array($line['Subregels'] ?? null) ? $line['Subregels'] : (json_decode((string) ($line['Subregels'] ?? ''), true) ?: []);
            $height = 5 * count($rows) + 4 * count($sub);
            if ($y + $height > 255) {
                $pdf->addPage();
                $y = 25;
            }
            if ($stripe) {
                $pdf->rect(20, $y - 4, 170, $height + 3, '#fafbfc');
            }
            $stripe = !$stripe;
            $qty = rtrim(rtrim(number_format((float) $line['Aantal'], 2, ',', ''), '0'), ',');
            $pdf->text(122, $y, $qty, 'R');
            $pdf->text(148, $y, money((int) $line['Prijs']), 'R');
            $pdf->text(162, $y, rtrim(rtrim((string) $line['BtwTarief'], '0'), '.') . '%', 'R');
            $pdf->text(188, $y, money((int) round((float) $line['Aantal'] * (int) $line['Prijs'])), 'R');
            foreach ($rows as $row) {
                $pdf->text(22, $y, $row);
                $y += 5;
            }
            if ($sub !== []) {
                $pdf->font('R', 8);
                foreach ($sub as $row) {
                    $pdf->textColor($primary);
                    $pdf->text(22, $y - 1.5, '·');
                    $pdf->textColor('#555555');
                    $pdf->text(26, $y - 1.5, (string) $row);
                    $y += 4;
                }
                $pdf->font('R', 9);
                $pdf->textColor('#111111');
            }
            $pdf->line(20, $y - 2, 190, $y - 2, '#e5e7eb');
            $y += 2;
        }

        // Totalen rechts.
        if ($y > 230) {
            $pdf->addPage();
            $y = 25;
        }
        $y += 4;
        foreach ([['Subtotaal', (int) $doc->Subtotaal], ['Btw', (int) $doc->Btw]] as [$k, $amount]) {
            $pdf->font('R', 9.5);
            $pdf->text(160, $y, $k, 'R');
            $pdf->text(188, $y, money($amount), 'R');
            $y += 6;
        }
        $pdf->rect(120, $y - 4.5, 70, 9, $primary);
        $pdf->font('B', 11);
        $pdf->textColor('#ffffff');
        $pdf->text(160, $y + 1.5, 'Totaal', 'R');
        $pdf->text(188, $y + 1.5, money((int) $doc->Totaal), 'R');
        $pdf->textColor('#111111');
        $y += 14;

        // Betaal-/notitieblok in grijze kaart.
        if ($doc->isQuote()) {
            $text = 'Deze offerte is geldig tot ' . nl_date((string) $doc->VervalDatum) . '. Je kunt de offerte online accepteren in je klantportaal: ' . absolute_url('/klant/documenten/' . $doc->id());
        } elseif ($doc->Status === 'betaald') {
            $text = 'Deze factuur is voldaan. Bedankt voor je betaling.';
        } elseif ((int) $doc->Totaal < 0) {
            $text = 'Het bedrag van deze creditfactuur wordt verrekend of teruggestort.';
        } else {
            $text = sprintf('Graag het bedrag van %s vóór %s overmaken op %s t.n.v. %s onder vermelding van %s.', money($doc->outstanding()), nl_date((string) $doc->VervalDatum), setting('bedrijf.iban', '-'), company_name(), $doc->Nummer);
        }
        $wrapped = $pdf->wrap($text, 162);
        $boxH2 = 5 * count($wrapped) + 6;
        if ($y + $boxH2 > 272) {
            $pdf->addPage();
            $y = 25;
        }
        $pdf->rect(20, $y - 4, 170, $boxH2, '#f6f7f8');
        $pdf->rect(20, $y - 4, 2, $boxH2, $primary);
        $pdf->font('R', 9);
        $pdf->textColor('#444444');
        foreach ($wrapped as $row) {
            $pdf->text(26, $y, $row);
            $y += 5;
        }
        $y += 6;

        $pdf->font('R', 9);
        $pdf->textColor('#444444');
        foreach (array_filter([(string) $doc->Opmerking, (string) setting($doc->isQuote() ? 'facturatie.voettekst_offerte' : 'facturatie.voettekst', '')]) as $extra) {
            $y = $pdf->paragraph(20, $y + 4, 170, $extra);
        }

        return $pdf->output();
    }
}
