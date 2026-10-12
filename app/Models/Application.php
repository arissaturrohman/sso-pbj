<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Client;

class Application extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'passport_client_id',
        'redirect_uri',
        'status',
    ];

    protected $hidden = [
        'passport_client_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function passportClient()
    {
        return $this->belongsTo(
            Client::class,
            'passport_client_id'
        );
    }

    /**
     * URL login (/sso/redirect) milik aplikasi client,
     * diturunkan otomatis dari redirect_uri yang terdaftar.
     *
     * Contoh: http://rup-app.test/sso/callback
     *         → http://rup-app.test/sso/redirect
     */
    public function getLoginUrlAttribute(): ?string
    {
        $base = $this->baseUrlFromRedirectUri();

        return $base ? $base . '/sso/redirect' : null;
    }

    /**
     * Ambil base URL (tanpa segmen callback terakhir)
     * dari redirect_uri yang terdaftar.
     */
    protected function baseUrlFromRedirectUri(): ?string
    {
        $uri = (string) $this->redirect_uri;

        if ($uri === '') {
            return null;
        }

        $parts = parse_url($uri);

        if (!isset($parts['host'])) {
            return null;
        }

        $scheme = $parts['scheme'] ?? 'http';
        $auth = isset($parts['user'])
            ? $parts['user'] . (isset($parts['pass']) ? ':' . $parts['pass'] : '') . '@'
            : '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        $path = $parts['path'] ?? '/';

        if ($path !== '/') {
            $path = rtrim($path, '/');

            // Potong segmen callback di akhir path (misal /sso/callback, /auth/callback, /callback)
            $path = preg_replace('#/(?:sso|auth|oauth)?/?callback$#i', '', $path);
        }

        return $scheme . '://' . $auth . $parts['host'] . $port . $path;
    }
}
