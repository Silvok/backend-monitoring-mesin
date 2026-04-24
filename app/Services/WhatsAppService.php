<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppService
{
    public function sendMessage(string $phone, string $message): array
    {
        if (!config('services.whatsapp.enabled')) {
            return ['ok' => false, 'reason' => 'whatsapp_disabled'];
        }

        $provider = strtolower((string) config('services.whatsapp.provider', 'fonnte'));

        return match ($provider) {
            'fonnte' => $this->sendViaFonnte($phone, $message),
            default => throw new RuntimeException("Unsupported WhatsApp provider: {$provider}"),
        };
    }

    private function sendViaFonnte(string $phone, string $message): array
    {
        $token = (string) config('services.whatsapp.token', '');
        $endpoint = (string) config('services.whatsapp.endpoint', 'https://api.fonnte.com/send');
        $timeout = (int) config('services.whatsapp.timeout', 15);

        if ($token === '') {
            throw new RuntimeException('WHATSAPP_TOKEN is missing.');
        }

        $normalizedPhone = $this->normalizePhone($phone);

        $response = Http::acceptJson()
            ->asForm()
            ->timeout($timeout)
            ->withHeaders([
                'Authorization' => $token,
            ])
            ->post($endpoint, [
                'target' => $normalizedPhone,
                'message' => $message,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('WhatsApp request failed with HTTP ' . $response->status());
        }

        $payload = $response->json();

        if (is_array($payload) && array_key_exists('status', $payload) && !$payload['status']) {
            throw new RuntimeException('WhatsApp provider rejected request: ' . json_encode($payload));
        }

        return [
            'ok' => true,
            'provider' => 'fonnte',
            'to' => $normalizedPhone,
            'response' => $payload,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            throw new RuntimeException('Phone number is empty after normalization.');
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return $digits;
    }
}
