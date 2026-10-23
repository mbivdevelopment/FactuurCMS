<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Pdf;
use App\Models\Document;

/** Factuur-/offerte-PDF in huisstijl: logo, bedrijfsgegevens, klant, regels, totalen, betaalinfo (A4, mm). */
final class DocumentPdf
{
    public static function render(Document $doc): string
    {
        $customer = $doc->customer();
        $primary = (string) setting('branding.kleur', '#4f46e5');
        $label = $doc->label();
        $pdf = new Pdf($label . ' ' . $doc->Nummer);
        $footer = static function (Pdf $pdf): void {
            $pdf->font('R', 7.5);
            $pdf->textColor('#888888');
            $pdf->text(105, 287, implode('  ·  ', array_filter([
                company_name(),
                setting('bedrijf.kvk') ? 'KvK ' . setting('bedrijf.kvk') : null,
                setting('bedrijf.btw') ? 'Btw ' . setting('bedrijf.btw') : null,
                setting('bedrijf.iban') ? 'IBAN ' . setting('bedrijf.iban') : null,
            ])), 'C');
        };
        $pdf->onPage(null, $footer);
        $pdf->addPage();
        $pdf->rect(0, 0, 210, 4, $primary);

        $logo = branding_path('logo');
        $logoOk = false;
        if ($logo !== '' && !str_ends_with(strtolower($logo), '.svg')) {
            try {
                $pdf->image($logo, 20, 14, 70, 22);
                $logoOk = true;
            } catch (\Throwable) {
                $logoOk = false;
            }
        }
        if (!$logoOk) {
            $pdf->font('B', 18);
            $pdf->textColor($primary);
            $pdf->text(20, 26, company_name());
        }

        $pdf->font('R', 8.5);
        $pdf->textColor('#555555');
        $y = 17;
        foreach (array_filter([
            company_name(),
            setting('bedrijf.adres'),
            trim(setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', '')),
            setting('bedrijf.email'),
            setting('bedrijf.telefoon'),
            setting('bedrijf.website'),
        ]) as $row) {
            $pdf->text(190, $y, (string) $row, 'R');
            $y += 4.3;
        }

        $pdf->textColor('#111111');
        $pdf->font('B', 10);
        $pdf->text(20, 56, (string) $customer?->display());
        $pdf->font('R', 10);
        $y = 61;
        foreach (array_filter([
            $customer?->Bedrijfsnaam ? $customer->name() : null,
            $customer?->Adres,
            trim(($customer?->Postcode ?? '') . ' ' . ($customer?->Plaats ?? '')),
            $customer?->BtwNummer ? 'Btw: ' . $customer->BtwNummer : null,
        ]) as $row) {
            $pdf->text(20, $y, (string) $row);
            $y += 5;
        }

        $pdf->font('B', 18);
        $pdf->textColor($primary);
        $pdf->text(20, 92, $label);
        $pdf->textColor('#111111');
        $meta = [
            [$label . 'nummer', (string) $doc->Nummer],
            [$doc->isQuote() ? 'Offertedatum' : 'Factuurdatum', nl_date((string) $doc->Datum)],
            [$doc->isQuote() ? 'Geldig tot' : 'Vervaldatum', nl_date((string) $doc->VervalDatum)],
            ['Klantnummer', (string) ($customer?->Nummer ?? '')],
        ];
        $x = 20;
        foreach ($meta as [$k, $v]) {
            $pdf->font('R', 7.5);
            $pdf->textColor('#888888');
            $pdf->text($x, 100, mb_strtoupper($k));
            $pdf->font('B', 9.5);
            $pdf->textColor('#111111');
            $pdf->text($x, 105, $v);
            $x += 44;
        }
        $y = 114;
        if ((string) $doc->Onderwerp !== '') {
            $pdf->font('R', 9.5);
            $pdf->text(20, $y, 'Betreft: ' . $doc->Onderwerp);
            $y += 7;
        }

        $y += 2;
        $pdf->rect(20, $y - 5, 170, 8, $primary);
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
        foreach ($doc->lines() as $line) {
            $rows = $pdf->wrap((string) $line['Omschrijving'], 92);
            if ($y + 6 * count($rows) > 255) {
                $pdf->addPage();
                $y = 25;
            }
            $qty = rtrim(rtrim(number_format((float) $line['Aantal'], 2, ',', ''), '0'), ',');
            $pdf->text(122, $y, $qty, 'R');
            $pdf->text(148, $y, money((int) $line['Prijs']), 'R');
            $pdf->text(162, $y, rtrim(rtrim((string) $line['BtwTarief'], '0'), '.') . '%', 'R');
            $pdf->text(188, $y, money((int) round((float) $line['Aantal'] * (int) $line['Prijs'])), 'R');
            foreach ($rows as $row) {
                $pdf->text(22, $y, $row);
                $y += 5;
            }
            $pdf->line(20, $y - 2, 190, $y - 2, '#e5e7eb');
            $y += 2;
        }

        if ($y > 230) {
            $pdf->addPage();
            $y = 25;
        }
        $y += 4;
        foreach ([['Subtotaal', (int) $doc->Subtotaal, 'R'], ['Btw', (int) $doc->Btw, 'R']] as [$k, $amount, $style]) {
            $pdf->font($style, 9.5);
            $pdf->text(160, $y, $k, 'R');
            $pdf->text(188, $y, money($amount), 'R');
            $y += 6;
        }
        $pdf->rect(120, $y - 4.5, 70, 8, '#f3f4f6');
        $pdf->font('B', 11);
        $pdf->text(160, $y + 1, 'Totaal', 'R');
        $pdf->text(188, $y + 1, money((int) $doc->Totaal), 'R');
        $y += 14;

        $pdf->font('R', 9);
        $pdf->textColor('#444444');
        if ($doc->isQuote()) {
            $text = 'Deze offerte is geldig tot ' . nl_date((string) $doc->VervalDatum) . '. Je kunt de offerte online accepteren in je klantportaal: ' . absolute_url('/klant/documenten/' . $doc->id());
        } elseif ($doc->Status === 'betaald') {
            $text = 'Deze factuur is voldaan. Bedankt voor je betaling.';
        } elseif ((int) $doc->Totaal < 0) {
            $text = 'Het bedrag van deze creditfactuur wordt verrekend of teruggestort.';
        } else {
            $text = sprintf('Graag het bedrag van %s vóór %s overmaken op %s t.n.v. %s onder vermelding van %s.', money($doc->outstanding()), nl_date((string) $doc->VervalDatum), setting('bedrijf.iban', '-'), company_name(), $doc->Nummer);
        }
        $y = $pdf->paragraph(20, $y, 170, $text);
        foreach (array_filter([(string) $doc->Opmerking, (string) setting($doc->isQuote() ? 'facturatie.voettekst_offerte' : 'facturatie.voettekst', '')]) as $extra) {
            $y = $pdf->paragraph(20, $y + 4, 170, $extra);
        }

        return $pdf->output();
    }
}
