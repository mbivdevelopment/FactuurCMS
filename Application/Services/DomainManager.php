<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Request;
use App\Models\Service;

/** Domeinbeheer: nameservers, EPP/verhuiskode, handover en opzeggen via Realtime Register. */
final class DomainManager
{
    /** Voert een domeinactie uit. @return array{0: string, 1: string} flash-type en -bericht */
    public static function run(Service $service, string $action, Request $request): array
    {
        $domain = $service->domainName();
        if ($domain === '') {
            return ['error', 'Voor deze dienst is geen domeinnaam bekend.'];
        }
        try {
            return match ($action) {
                'ns' => self::nameservers($service, $domain, $request),
                'epp' => self::epp($service, $domain),
                'handover' => self::handover($service, $domain, $request),
                'opzeggen' => self::cancel($service, $domain),
                'hervatten' => self::resume($service, $domain),
                default => ['error', 'Onbekende domeinactie.'],
            };
        } catch (\Throwable $e) {
            return ['error', 'Domeinactie mislukt: ' . $e->getMessage()];
        }
    }

    private static function nameservers(Service $service, string $domain, Request $request): array
    {
        $ns = [];
        for ($i = 1; $i <= 6; $i++) {
            $v = trim((string) $request->input('ns' . $i));
            if ($v !== '') {
                $ns[] = $v;
            }
        }
        if ($ns === []) {
            $ns = preg_split('/[\s,]+/', (string) $request->input('ns')) ?: [];
        }
        RealtimeRegister::setNameservers($domain, $ns);
        $service->putData('nameservers', array_values(array_filter($ns)));

        return ['success', 'Nameservers van ' . $domain . ' zijn gewijzigd.'];
    }

    private static function epp(Service $service, string $domain): array
    {
        $info = RealtimeRegister::info($domain);
        if ($info['authcode'] === '') {
            return ['error', 'Realtime Register gaf geen verhuiskode terug voor ' . $domain . '.'];
        }
        $service->putData('epp', $info['authcode']);

        return ['success', 'Verhuiskode opgehaald.'];
    }

    /** Handover: domein overplaatsen naar een andere klant — mail met overnamecode + link. */
    private static function handover(Service $service, string $domain, Request $request): array
    {
        $email = strtolower(trim((string) $request->input('email')));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return ['error', 'Vul een geldig e-mailadres van de nieuwe klant in.'];
        }
        $code = strtoupper(bin2hex(random_bytes(4)));
        $service->putData('handover', ['code' => $code, 'email' => $email, 'op' => date('Y-m-d H:i')]);
        $link = absolute_url('/klant/diensten?overname=' . rawurlencode($code));
        $body = "Je kunt het domein {$domain} overnemen.\n\nOvernamecode: {$code}\n\nLog in en ga naar: {$link}\n\nDeze code blijft geldig tot de overname is afgerond of ingetrokken.";
        \App\Core\Mailer::send($email, 'Domein overnemen: ' . $domain, $body);

        return ['success', 'Handover gestart: ' . $email . ' heeft een mail met overnamecode gekregen.'];
    }

    /** Beheeracties voor niet-domeindiensten (webhosting e.d.): handover, opzeggen, hervatten. @return array{0: string, 1: string} */
    public static function runHosting(Service $service, string $action, Request $request): array
    {
        $label = $service->domainName() !== '' ? $service->domainName() : (string) $service->Naam;
        try {
            return match ($action) {
                'handover' => self::handover($service, $label, $request),
                'opzeggen' => self::cancelService($service),
                'hervatten' => self::resumeService($service),
                default => ['error', 'Onbekende actie.'],
            };
        } catch (\Throwable $e) {
            return ['error', 'Actie mislukt: ' . $e->getMessage()];
        }
    }

    private static function cancelService(Service $service): array
    {
        if ((string) $service->Bron === 'qevanto' && (int) $service->ExternId > 0) {
            QevantoPaneel::setStatus((int) $service->ExternId, 'suspended');
        }
        $service->Status = 'opgezegd';
        $service->EindDatum = $service->VolgendeFactuur ?: date('Y-m-d');
        $service->save();

        return ['success', 'De dienst is opgezegd' . ($service->Bron === 'qevanto' ? ' en in het paneel opgeschort' : '') . '.'];
    }

    private static function resumeService(Service $service): array
    {
        if ((string) $service->Bron === 'qevanto' && (int) $service->ExternId > 0) {
            QevantoPaneel::setStatus((int) $service->ExternId, 'active');
        }
        $service->Status = 'actief';
        $service->EindDatum = null;
        $service->save();

        return ['success', 'De opzegging is teruggedraaid; de dienst is weer actief.'];
    }

    /** Klant neemt een domein over met de gemailde code. @return array{0: string, 1: string} */
    public static function claim(string $code, int $klantId): array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return ['error', 'Vul een overnamecode in.'];
        }
        foreach (\App\Models\Service::query("SELECT * FROM Diensten WHERE Gegevens LIKE ?", ['%"code":"' . $code . '"%']) as $service) {
            $g = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []);
            $h = $g['handover'] ?? null;
            if (!is_array($h) || (string) ($h['code'] ?? '') !== $code) {
                continue;
            }
            $service->KlantId = $klantId;
            unset($g['handover']);
            $service->Gegevens = json_encode($g, JSON_UNESCAPED_UNICODE);
            $service->save();

            return ['success', $service->domainName() !== '' ? 'Domein ' . $service->domainName() . ' is overgenomen en staat nu bij jouw diensten.' : 'De dienst is overgenomen.'];
        }

        return ['error', 'Onbekende of verlopen overnamecode.'];
    }

    /** Opzeggen: domein laten aflopen op de verloopdatum. */
    private static function cancel(Service $service, string $domain): array
    {
        $info = [];
        try {
            $info = RealtimeRegister::info($domain);
            if ($info['autoRenew']) {
                RealtimeRegister::setAutoRenew($domain, false);
            }
        } catch (\Throwable) {
        }
        $service->Status = 'opgezegd';
        $service->EindDatum = $info['expiry'] ?? $service->EindDatum;
        $service->save();

        return ['success', $domain . ' is opgezegd en loopt af' . (!empty($info['expiry']) ? ' op ' . nl_date($info['expiry']) : '') . '.'];
    }

    /** Opzegging terugdraaien: autorenew weer aan. */
    private static function resume(Service $service, string $domain): array
    {
        RealtimeRegister::setAutoRenew($domain, true);
        $service->Status = 'actief';
        $service->EindDatum = null;
        $service->save();

        return ['success', $domain . ' wordt weer automatisch verlengd.'];
    }
}
