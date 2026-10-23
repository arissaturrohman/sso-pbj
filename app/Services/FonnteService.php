<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FonnteService
{
    /**
     * Mengirim pesan WhatsApp melalui Fonnte.
     */
    public function sendMessage(string $target, string $message): array
    {
        $token = config('services.fonnte.token');
        $url = config('services.fonnte.url');

        if (empty($token)) {
            throw new RuntimeException(
                'Token Fonnte belum dikonfigurasi.'
            );
        }

        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => $token,
            ])
            ->timeout(20)
            ->post($url, [
                'target' => $target,
                'message' => $message,
                'countryCode' => (string) config(
                    'services.fonnte.country_code',
                    '62'
                ),
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Fonnte gagal memproses permintaan HTTP.'
            );
        }

        $result = $response->json();

        if (
            !is_array($result) ||
            !($result['status'] ?? false)
        ) {
            throw new RuntimeException(
                'Pengiriman WhatsApp gagal: ' .
                ($result['reason'] ?? $result['detail'] ?? 'Respons tidak valid.')
            );
        }

        return $result;
    }
}