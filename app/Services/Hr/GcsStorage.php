<?php

namespace App\Services\Hr;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Minimal Google Cloud Storage client (JSON API + service-account JWT).
 *
 * The project has no GCS SDK installed, and adding one to the EC2 box just for
 * resume files would pull a large dependency tree — the two calls we need
 * (upload, download) are small enough to do directly.
 */
class GcsStorage
{
    private ?array $account = null;

    public function configured(): bool
    {
        return $this->account !== null
            && (string) config('hr.storage.bucket', '') !== '';
    }

    public function upload(string $key, string $contents, string $contentType = 'application/octet-stream'): bool
    {
        $token = $this->accessToken();
        $bucket = rawurlencode((string) config('hr.storage.bucket'));

        if ($token === null) {
            return false;
        }

        $response = Http::withToken($token)
            ->connectTimeout(10)
            ->timeout(120)
            ->withHeaders(['Content-Type' => $contentType])
            ->withBody($contents, $contentType)
            ->post('https://storage.googleapis.com/upload/storage/v1/b/' . $bucket . '/o?uploadType=media&name=' . rawurlencode($key));

        return $response->successful();
    }

    public function download(string $key): ?string
    {
        $token = $this->accessToken();

        if ($token === null) {
            return null;
        }

        $response = Http::withToken($token)
            ->connectTimeout(10)
            ->timeout(120)
            ->get('https://storage.googleapis.com/storage/v1/b/'
                . rawurlencode((string) config('hr.storage.bucket'))
                . '/o/' . rawurlencode($key) . '?alt=media');

        return $response->successful() ? $response->body() : null;
    }

    public function delete(string $key): void
    {
        $token = $this->accessToken();

        if ($token === null) {
            return;
        }

        Http::withToken($token)
            ->connectTimeout(10)
            ->timeout(30)
            ->delete('https://storage.googleapis.com/storage/v1/b/'
                . rawurlencode((string) config('hr.storage.bucket'))
                . '/o/' . rawurlencode($key));
    }

    /** Full object key for a resume, e.g. resumes/portal/APP-XXXX.pdf. */
    public function resumeKey(string $relative): string
    {
        $prefix = trim((string) config('hr.storage.prefix', 'resumes'), '/');

        return $prefix !== '' ? $prefix . '/' . ltrim($relative, '/') : ltrim($relative, '/');
    }

    private function accessToken(): ?string
    {
        $account = $this->account;

        if ($account === null) {
            return null;
        }

        $cacheKey = 'hr:gcs:token:' . sha1((string) ($account['client_email'] ?? ''));

        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $now = time();
        $header = $this->b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = $this->b64(json_encode([
            'iss' => (string) $account['client_email'],
            'scope' => 'https://www.googleapis.com/auth/devstorage.read_write',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signature = '';
        $privateKey = openssl_pkey_get_private((string) $account['private_key']);

        if ($privateKey === false || !openssl_sign($header . '.' . $claims, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            return null;
        }

        $assertion = $header . '.' . $claims . '.' . $this->b64($signature);

        $response = Http::asForm()
            ->connectTimeout(10)
            ->timeout(30)
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ]);

        if (!$response->successful()) {
            return null;
        }

        $token = (string) $response->json('access_token');
        $expiresIn = (int) $response->json('expires_in', 3600);

        if ($token === '') {
            return null;
        }

        Cache::put($cacheKey, $token, max(60, $expiresIn - 60));

        return $token;
    }

    private function b64(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    /** Load the service account once per request. */
    private function loadAccount(): ?array
    {
        if ($this->account !== null) {
            return $this->account;
        }

        $json = (string) config('hr.storage.credentials_json', '');
        $path = (string) config('hr.storage.credentials_path', '');

        if ($json === '' && $path !== '' && is_readable($path)) {
            $json = (string) file_get_contents($path);
        }

        if ($json === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        if (!is_array($decoded) || empty($decoded['client_email']) || empty($decoded['private_key'])) {
            return null;
        }

        $this->account = $decoded;

        return $this->account;
    }

    public function __construct()
    {
        $this->account = $this->loadAccount();
    }
}
