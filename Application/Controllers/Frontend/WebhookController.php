<?php

declare(strict_types=1);

namespace App\Controllers\Frontend;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\Cryptomus;
use App\Services\OnlinePayment;

/** Betaal-webhooks; de status wordt altijd bij de provider gecontroleerd of via de handtekening geverifieerd. */
final class WebhookController extends Controller
{
    public function mollie(Request $request): Response
    {
        $row = OnlinePayment::findByExtern('mollie', $request->string('id'));
        if ($row !== null) {
            try {
                OnlinePayment::sync($row);
            } catch (\Throwable) {
                return new Response('error', 500, ['Content-Type' => 'text/plain']);
            }
        }

        return new Response('ok', 200, ['Content-Type' => 'text/plain']);
    }

    public function cryptomus(Request $request): Response
    {
        $data = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($data) || !Cryptomus::verify($data)) {
            return new Response('invalid sign', 400, ['Content-Type' => 'text/plain']);
        }
        $row = OnlinePayment::findByExtern('cryptomus', (string) ($data['uuid'] ?? ''));
        if ($row !== null) {
            OnlinePayment::apply((int) $row['Id'], (string) ($data['status'] ?? ''));
        }

        return new Response('ok', 200, ['Content-Type' => 'text/plain']);
    }
}
