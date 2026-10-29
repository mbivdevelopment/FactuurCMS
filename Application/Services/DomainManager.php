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
                'handover' => self::handover($service, $domain),
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
        $ns = preg_split('/[\s,]+/', (string) $request->input('ns')) ?: [];
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

    /** Handover: domein wegverhuizen — verhuiskode ophalen en autorenew uit. */
    private static function handover(Service $service, string $domain): array
    {
        $info = RealtimeRegister::info($domain);
        if ($info['autoRenew']) {
            RealtimeRegister::setAutoRenew($domain, false);
        }
        $service->putData('handover', ['code' => $info['authcode'], 'op' => date('Y-m-d H:i')]);

        return ['success', 'Handover gestart' . ($info['authcode'] !== '' ? ': verhuiskode ' . $info['authcode'] : '') . '. Automatisch verlengen staat uit.'];
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
