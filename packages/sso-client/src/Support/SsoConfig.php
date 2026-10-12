<?php

namespace Demak\SsoClient\Support;

/**
 * Pembaca konfigurasi terpusat. Urutan sumber: config/sso-client.php
 * (punya prioritas), fallback ke config/services.sso (milik rup-app),
 * supaya .env lama tetap jalan tanpa diubah.
 */
class SsoConfig
{
    public function baseUrl(): string
    {
        return rtrim((string) ($this->get('base_url') ?? $this->service('base_url')), '/');
    }

    public function clientId(): ?string
    {
        $value = $this->get('client_id') ?? $this->service('client_id');

        return ($value === '' || $value === null) ? null : (string) $value;
    }

    public function clientSecret(): ?string
    {
        $value = $this->get('client_secret') ?? $this->service('client_secret');

        return ($value === '' || $value === null) ? null : (string) $value;
    }

    public function redirectUri(): ?string
    {
        $value = $this->get('redirect_uri') ?? $this->service('redirect_uri');

        return ($value === '' || $value === null) ? null : (string) $value;
    }

    public function scope(): string
    {
        return (string) ($this->get('scope', 'user:read') ?: 'user:read');
    }

    public function userApi(): string
    {
        $path = (string) ($this->get('user_api', '/api/user') ?: '/api/user');

        return str_starts_with($path, '/') ? $path : '/' . $path;
    }

    public function timeout(): int
    {
        return (int) ($this->get('timeout', 15) ?: 15);
    }

    public function loginRoute(): string
    {
        return (string) ($this->get('login_route', 'login') ?: 'login');
    }

    public function homeRoute(): string
    {
        return (string) ($this->get('home_route', 'home') ?: 'home');
    }

    public function ready(): bool
    {
        return $this->baseUrl() !== ''
            && $this->clientId() !== null
            && $this->redirectUri() !== null;
    }

    private function get(string $key, mixed $default = null): mixed
    {
        return config('sso-client.' . $key, $default);
    }

    private function service(string $key): mixed
    {
        return config('services.sso.' . $key);
    }
}
