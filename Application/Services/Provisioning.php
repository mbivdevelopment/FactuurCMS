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
        if ($service === null || $product === null || $product->Bron !== 'qevanto' || !str_starts_with((string) $product->ExternId, 'pakket-') || (string) $service->ExternId !== '') {
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
                'Gegevens' => json_encode(['qevanto' => ['nummer' => $sub['number'] ?? null, 'systeemgebruiker' => $sub['unixUser'] ?? null, 'pakket' => $product->Naam, 'automatisch' => date('Y-m-d H:i')]], JSON_UNESCAPED_UNICODE),
                'Status' => 'actief',
                'BijgewerktOp' => now(),
            ], ['Id' => $serviceId]);
            Log::db('qevanto', 'Automatisch aangemaakt: ' . ($sub['number'] ?? '') . ' voor ' . $email, $customer?->id());
        } catch (\Throwable $e) {
            Log::db('qevanto', 'Automatisch aanmaken mislukt voor dienst ' . $service->Nummer . ': ' . $e->getMessage(), $customer?->id());
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
