<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Koneksi ke server SSO
    |--------------------------------------------------------------------------
    |
    | Nama env-nya disamakan dengan yang sudah dipakai rup-app
    | (services.sso) supaya template .env bisa disalin mentah-mentah.
    |
    */
    'base_url' => env('SSO_BASE_URL'),
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI'),

    // Scope yang diminta ke server SSO.
    'scope' => env('SSO_SCOPE', 'user:read'),

    // Path API profil di server SSO (diawali slash).
    'user_api' => env('SSO_USER_API', '/api/user'),

    // Timeout HTTP ke server SSO (detik).
    'timeout' => (int) env('SSO_TIMEOUT', 15),

    // Route lokal untuk login manual (fallback) dan home setelah login.
    'login_route' => env('SSO_LOGIN_ROUTE', 'login'),
    'home_route' => env('SSO_HOME_ROUTE', 'home'),

    /*
    |--------------------------------------------------------------------------
    | Model user lokal + cara mencocokkannya dengan profil SSO
    |--------------------------------------------------------------------------
    |
    | Default: cari by NIP dulu (prioritas), kalau profil tidak bawa NIP
    | cari by email case-insensitive, lalu pastikan status aktif.
    | Menerima status 'active' (string) maupun true/1 (boolean).
    |
    | Kalau skema user aplikasi berbeda, buat class sendiri yang
    | mengimplementasikan Demak\SsoClient\Contracts\SsoUserResolver
    | lalu arahkan 'resolver' ke class tersebut (lihat stubs/).
    |
    */
    'user_model' => env('SSO_USER_MODEL', 'App\\Models\\User'),
    'resolver' => env('SSO_RESOLVER', Demak\SsoClient\Resolvers\DefaultUserResolver::class),

    /*
    |--------------------------------------------------------------------------
    | Route bawaan package
    |--------------------------------------------------------------------------
    |
    | Menyediakan GET /sso/redirect, /sso/auto-login, /sso/callback
    | dengan nama sso.redirect, sso.auto-login, sso.callback.
    |
    | Matikan (SSO_CLIENT_ROUTES=false) kalau aplikasi sudah punya
    | route/controller SSO sendiri (mis. rup-app saat ini).
    |
    */
    'routes' => [
        'enabled' => env('SSO_CLIENT_ROUTES', true),
        'middleware' => ['web'],
        'prefix' => 'sso',
    ],
];
