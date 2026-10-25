<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Log;
use App\Models\Product;
use App\Models\Service;

/** Na betaling: Qevanto-pakketten automatisch aanmaken in het hostingpaneel (klant + abonnement). */
final class Provisioning
{
    public static function run(int $serviceId): void
    {
        $service = Service::find($serviceId);
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
}
