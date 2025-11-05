<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class KKUApiService
{
    protected string $apiBase;
    protected ?string $clientId;
    protected ?string $secretKey;
    protected string $tokenCacheKey;
    protected int $tokenTtlMinutes;

    public function __construct()
    {
        $cfg = config('services.kku');
        $this->apiBase = rtrim(Arr::get($cfg, 'api_base', 'https://api.kku.ac.th/v3'), '/');
        $this->clientId = Arr::get($cfg, 'client_id');
        $this->secretKey = Arr::get($cfg, 'secret_key');
        $this->tokenCacheKey = Arr::get($cfg, 'token_cache_key', 'kku_api_token');
        $this->tokenTtlMinutes = (int) Arr::get($cfg, 'token_ttl_minutes', 23 * 60);
    }

    public function getToken(bool $forceRefresh = false): string
    {
        if (!$forceRefresh) {
            $cached = cache($this->tokenCacheKey);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        $resp = Http::asForm()->post($this->apiBase . '/auth/token', [
            'client_id'  => $this->clientId,
            'secret_key' => $this->secretKey,
        ]);

        if ($resp->failed()) {
            $status = $resp->status();
            $body = $resp->body();
            Log::error('KKU Token Response (failed)', ['status' => $status, 'body' => $body]);
            throw new \RuntimeException("KKU token request failed: HTTP {$status} - {$body}");
        }

        // Some APIs return HTTP 200 with error payload. Guard for that.
        $json = $resp->json() ?: [];
        $payloadStatus = is_array($json) ? ($json['status'] ?? null) : null;
        $payloadCode = is_array($json) ? ($json['code'] ?? null) : null;
        if (is_numeric($payloadStatus) && (int) $payloadStatus >= 400) {
            Log::error('KKU Token Response (api-error)', ['status' => $payloadStatus, 'code' => $payloadCode, 'body' => $resp->body()]);
            throw new \RuntimeException("KKU token API error: status={$payloadStatus} code={$payloadCode}");
        }

        $token = (string) ($json['token'] ?? '');
        if ($token === '') {
            Log::error('KKU Token Response', ['body' => $resp->body()]);
            throw new \RuntimeException('KKU token missing in response');
        }

        cache([$this->tokenCacheKey => $token], now()->addMinutes($this->tokenTtlMinutes));
        return $token;
    }

    public function sendMail(array $payload): array
    {
        $token = $this->getToken();

        $resp = Http::asForm()
            ->withToken($token)
            ->post($this->apiBase . '/email/send', [
                'from'      => $payload['from'] ?? null,
                'fromName'  => $payload['fromName'] ?? null,
                'to'        => $payload['to'] ?? null,
                'subject'   => $payload['subject'] ?? null,
                'message'   => $payload['message'] ?? null,
                // Optional
                'cc'        => $payload['cc'] ?? null,
                'bcc'       => $payload['bcc'] ?? null,
            ]);

        if ($resp->failed()) {
            return [
                'ok' => false,
                'status' => $resp->status(),
                'data' => $resp->json() ?: ['error' => $resp->body()],
            ];
        }

        return [
            'ok' => true,
            'status' => $resp->status(),
            'data' => $resp->json(),
        ];
    }
}
