<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Crypt;
use App\Core\Database;
use App\Core\Log;
use App\Models\Product;
use App\Models\Service;

/** Na betaling automatisch aanmaken: domeinen via Realtime Register, Qevanto-pakketten in het hostingpaneel. */
final class Provisioning
{
    public static function run(int $serviceId): void
    {
        $service = Service::find($serviceId);
        if ($service !== null && is_array($service->Gegevens['domein'] ?? null) && (string) $service->Bron === '') {
            self::domain($service);

            return;
        }
        $product = $service !== null && $service->ProductId ? Product::find((int) $service->ProductId) : null;
        if ($service === null || $product === null || (string) $service->ExternId !== '') {
            return;
        }
        if ($product->Bron === 'interserver') {
            self::vps($service);

            return;
        }
        if ($product->Bron !== 'qevanto' || !str_starts_with((string) $product->ExternId, 'pakket-')) {
            return;
        }
        $customer = $service->customer();
        try {
            $email = strtolower((string) $customer?->Email);
            $panelUser = null;
            foreach (QevantoPaneel::customers($email) as $u) {
                if (strtolower((string) ($u['email'] ?? '')) === $email) {
                    $panelUser = $u;
                }
            }
            $panelUser ??= QevantoPaneel::post('/api/customers', [
                'name' => trim($customer->Voornaam . ' ' . $customer->Achternaam) ?: $email,
                'email' => $email,
                'company' => $customer->Bedrijfsnaam ?: null,
                'phone' => $customer->Telefoon ?: null,
                'password' => bin2hex(random_bytes(12)),
            ]);
            $sub = QevantoPaneel::post('/api/subscriptions', [
                'userId' => (int) $panelUser['id'],
                'packageId' => (int) substr((string) $product->ExternId, 7),
                'label' => (string) ($service->Omschrijving ?: $product->Naam),
            ]);
            Database::update('Diensten', [
                'Bron' => 'qevanto',
                'ExternId' => (string) ($sub['id'] ?? ''),
                'Gegevens' => json_encode(['qevanto' => ['nummer' => $sub['number'] ?? null, 'systeemgebruiker' => $sub['unixUser'] ?? null, 'pakket' => $product->Naam, 'kind' => $sub['package']['kind'] ?? null, 'automatisch' => date('Y-m-d H:i')]], JSON_UNESCAPED_UNICODE),
                'Status' => 'actief',
                'BijgewerktOp' => now(),
            ], ['Id' => $serviceId]);
            Log::db('qevanto', 'Automatisch aangemaakt: ' . ($sub['number'] ?? '') . ' voor ' . $email, $customer?->id());
        } catch (\Throwable $e) {
            Log::db('qevanto', 'Automatisch aanmaken mislukt voor dienst ' . $service->Nummer . ': ' . $e->getMessage(), $customer?->id());
        }
    }

    /** CloudVPS bestellen bij InterServer; root-wachtwoord versleuteld in Gegevens. */
    private static function vps(Service $service): void
    {
        $customer = $service->customer();
        $geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []);
        $cfg = (array) ($geo['interserver'] ?? []);
        try {
            $rootpass = bin2hex(random_bytes(9)) . 'aA1!';
            $result = Interserver::order($cfg + [
                'controlpanel' => 'none',
                'rootpass' => $rootpass,
                'comment' => 'Bestelling ' . $service->Nummer . ' (' . ($customer?->Email ?? '') . ')',
            ]);
            $vpsId = (int) ($result['vps_id'] ?? $result['id'] ?? 0);
            if ($vpsId === 0) {
                throw new \RuntimeException('InterServer gaf geen VPS-id terug: ' . json_encode($result));
            }
            $geo['interserver'] = $cfg + [
                'rootpass' => \App\Core\Crypt::encrypt($rootpass),
                'besteld' => date('Y-m-d H:i'),
            ];
            Database::update('Diensten', [
                'Bron' => 'interserver',
                'ExternId' => (string) $vpsId,
                'Gegevens' => json_encode($geo, JSON_UNESCAPED_UNICODE),
                'Status' => 'actief',
                'BijgewerktOp' => now(),
            ], ['Id' => $service->id()]);
            Log::db('interserver', 'VPS #' . $vpsId . ' besteld voor ' . ($customer?->Email ?? ''), $customer?->id());
        } catch (\Throwable $e) {
            Log::db('interserver', 'VPS bestellen mislukt voor dienst ' . $service->Nummer . ': ' . $e->getMessage(), $customer?->id());
        }
    }

    /** Domein registreren of verhuizen bij Realtime Register. Zonder koppeling of met automatisch uit doe je het zelf. */
    private static function domain(Service $service): void
    {
        if (!RealtimeRegister::configured() || (string) setting('rr.auto', '1') !== '1') {
            return;
        }
        $customer = $service->customer();
        $info = (array) $service->Gegevens['domein'];
        $domain = (string) ($info['naam'] ?? $service->Naam);
        try {
            if ($customer === null) {
                throw new \RuntimeException('Klant niet gevonden.');
            }
            $authcode = !empty($info['authcode']) ? Crypt::decrypt((string) $info['authcode']) : null;
            $result = RealtimeRegister::register($domain, RealtimeRegister::ensureContact($customer), $info['actie'] === 'verhuizen' ? $authcode : null, array_map('strval', (array) ($info['ns'] ?? [])));
            unset($info['authcode']);
            Database::update('Diensten', [
                'Bron' => 'realtimeregister',
                'ExternId' => $domain,
                'Gegevens' => json_encode(['domein' => $info, 'realtimeregister' => ['processId' => $result['processId'] ?? null, 'status' => $info['actie'] === 'verhuizen' ? 'verhuizing aangevraagd' : 'geregistreerd', 'verloopt' => isset($result['expiryDate']) ? date('Y-m-d', (int) strtotime((string) $result['expiryDate'])) : null, 'automatisch' => date('Y-m-d H:i')]], JSON_UNESCAPED_UNICODE),
                'Status' => 'actief',
                'BijgewerktOp' => now(),
            ], ['Id' => $service->id()]);
            Log::db('realtimeregister', ($info['actie'] === 'verhuizen' ? 'Verhuizing aangevraagd: ' : 'Geregistreerd: ') . $domain, $customer->id());
        } catch (\Throwable $e) {
            Log::db('realtimeregister', 'Domein ' . $domain . ' (dienst ' . $service->Nummer . ') niet vastgelegd: ' . $e->getMessage(), $customer?->id());
        }
    }
}
