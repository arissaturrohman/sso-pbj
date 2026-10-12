# SSO Demak (sso-pbj)

Server Single Sign-On (SSO) pusat untuk aplikasi Pemkab Demak berbasis **Laravel 12 + Laravel Passport v13** dengan alur **OAuth2 Authorization Code Grant + PKCE**.

Sistem ini mengimplementasikan **True SSO**: user login sekali di server SSO, lalu bisa mengakses semua aplikasi client terdaftar tanpa perlu login ulang maupun konfirmasi "Izinkan akses".

---

## 🎯 Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| **Central Login** | User login di `sso-pbj.test/login` → dipaksa ke dashboard `/apps` |
| **Dashboard Aplikasi** | Halaman `/apps` menampilkan semua aplikasi client terdaftar (grid card) |
| **Auto-Consent** | `Client::skipsAuthorization()` → `return $this->isActive()` → **tidak ada lagi halaman "Izinkan akses"** untuk aplikasi aktif |
| **Launch di Tab Baru** | Klik "Buka Aplikasi" → `target="_blank"` ke `/sso/redirect` milik client → langsung login (session SSO aktif + skip consent) |
| **Auto-Redirect Diam-Diam** | User ketik URL client langsung (`rup-app.test`) → overlay 1,5 dtk → redirect ke SSO → balik sudah login |
| **Logout Terpusat** | Logout di client → redirect ke `/apps` SSO |
| **Multi-Aplikasi** | Mendukung banyak aplikasi client (RUP, E-Monev, dll.) |

---

## 🏗️ Arsitektur

```
┌─────────────────┐        OAuth2 + PKCE        ┌──────────────────┐
│   sso-pbj.test  │ ◄─────────────────────────► │  rup-app.test    │
│   (SSO Server)  │   skip-consent + session    │  (Client App)    │
│                 │                             │                  │
│  • /login       │                             │  • /sso/redirect │
│  • /apps        │                             │  • /sso/callback │
│  • /oauth/*     │                             │  • auto-redirect │
└─────────────────┘                             └──────────────────┘
        ▲                                               │
        │           (logout redirect)                   │
        └───────────────────────────────────────────────┘
```

---

## 📋 Daftar Isi Dokumentasi

0. [Cara Cepat Menambah Aplikasi Client Baru](#0-cara-cepat-menambah-aplikasi-client-baru--checklist--10-menit)
1. [Instalasi & Setup Server SSO](#1-instalasi--setup-server-sso)
2. [Pendaftaran Aplikasi Client di SSO](#2-pendaftaran-aplikasi-client-di-sso)
3. [Integrasi Aplikasi Client Baru (via Composer VCS)](#3-integrasi-aplikasi-client-baru-via-composer-vcs)
4. [Konfigurasi Environment Client](#4-konfigurasi-environment-client)
5. [Memasang Komponen UI di Halaman Login](#5-memasang-komponen-ui-di-halaman-login)
6. [Kustomisasi Resolver User](#6-kustomisasi-resolver-user)
7. [Alur Kerja Lengkap (End-to-End)](#7-alur-kerja-lengkap-end-to-end)
8. [Troubleshooting](#8-troubleshooting)

---

## 0. Cara Cepat Menambah Aplikasi Client Baru (Checklist ± 10 Menit)

Panduan ringkas untuk menambahkan aplikasi client baru (mis. `e-monev.test`). Detail tiap langkah ada di bagian 2–6 di bawah.

### Ringkasan Checklist

| # | Di mana | Yang dilakukan |
|---|---------|----------------|
| 1 | Aplikasi client | Pastikan aplikasi Laravel siap, punya model `App\Models\User` & route `home` |
| 2 | Terminal client | Tambah repo VCS + install `pemkab-demak/sso-client` |
| 3 | `.env` client | Isi blok `SSO_*` (base_url, client_id, redirect_uri) |
| 4 | Admin SSO (`sso-pbj`) | Daftarkan aplikasi → catat **Client ID** |
| 5 | Logout client | Ubah redirect logout ke `/apps` SSO |
| 6 | Halaman login client | Pasang `<x-sso-client::sso-button />` + `<x-sso-client::auto-redirect />` |
| 7 | Terminal client | `php artisan config:clear && route:clear` |
| 8 | Browser | Uji dari Dashboard SSO `/apps` |

---

### Langkah 1 — Siapkan Aplikasi Client

Syarat minimum aplikasi client:
- Laravel 11/12, sudah punya autentikasi lokal (`config/auth.php` normal).
- Ada model user (default `App\Models\User`) dengan kolom **`nip`** atau **`email`**, dan kolom **`status`**.
- Memiliki route bernama `home` (dipakai sebagai halaman tujuan setelah login SSO). Cek dengan:

```bash
php artisan route:list --name=home
```

> Jika aplikasi tidak punya route `home`, set alternatifnya di `.env`: `SSO_HOME_ROUTE=admin.dashboard`.

---

### Langkah 2 — Install Package SSO Client

Edit `composer.json` aplikasi client, tambahkan repository + require:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/arissaturrohman/sso-pbj.git"
    }
],
"require": {
    "pemkab-demak/sso-client": "^1.0"
}
```

Lalu jalankan:

```bash
composer update pemkab-demak/sso-client
```

Package otomatis mendaftarkan route `/sso/redirect`, `/sso/auto-login`, `/sso/callback` dan komponen Blade.

---

### Langkah 3 — Konfigurasi `.env` Client

Buka `.env` aplikasi client (mis. `e-monev.test`) dan tambahkan:

```env
SSO_BASE_URL=http://sso-pbj.test
SSO_CLIENT_ID=2
SSO_CLIENT_SECRET=
SSO_REDIRECT_URI=http://e-monev.test/sso/callback

# Opsional
# SSO_HOME_ROUTE=home
# SSO_LOGIN_ROUTE=login
# SSO_SCOPE=user:read
```

> ⚠️ **Penting:** `SSO_REDIRECT_URI` **harus sama persis** dengan Redirect URI yang didaftarkan di Langkah 4 (termasuk `http`/`https` dan tanpa trailing slash).

Konfigurasi `config/services.php` client (opsional, jika ingin memakai `config('services.sso.*')` di kode sendiri):

```php
'sso' => [
    'base_url'     => env('SSO_BASE_URL'),
    'client_id'    => env('SSO_CLIENT_ID'),
    'client_secret'=> env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI'),
],
```

---

### Langkah 4 — Daftarkan Aplikasi di Server SSO

1. Login ke `http://sso-pbj.test/login` sebagai admin.
2. Buka menu **Admin → Aplikasi SSO** (`/admin/applications`), klik **Daftar Aplikasi**.
3. Isi form:

| Field | Nilai contoh | Catatan |
|-------|--------------|---------|
| Nama Aplikasi | `E-Monev` | Nama tampil di dashboard `/apps` |
| Slug | `e-monev` | Unik, lowercase, tanpa spasi |
| Deskripsi | `Monitoring evaluasi kegiatan` | Opsional |
| Redirect URI | `http://e-monev.test/sso/callback` | **Harus sama dengan `.env` client** |

4. Klik **Daftarkan Aplikasi**.
5. Sistem otomatis membuat OAuth Client Passport (public client + PKCE). Aplikasi baru langsung berstatus **Aktif**.
6. Buka kembali daftar aplikasi → **salin nilai kolom `Client ID`** (yaitu `passport_client_id`, mis. `2`).
7. Tempel ke `.env` client sebagai `SSO_CLIENT_ID` (Langkah 3).

---

### Langkah 5 — Arahkan Logout Client ke Dashboard SSO

Cari method `logout()` di controller auth aplikasi client, lalu ubah redirect-nya:

```php
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Kembali ke Dashboard SSO, bukan halaman login lokal
    return redirect(
        rtrim(config('services.sso.base_url'), '/') . '/apps'
    );
}
```

> Tambahkan blok `'sso' => [...]` di `config/services.php` jika belum ada (lihat Langkah 3).

---

### Langkah 6 — Pasang Komponen di Halaman Login Client

Buka `resources/views/auth/login.blade.php`, tambahkan setelah form login manual:

```blade
{{-- Tombol Login SSO --}}
<x-sso-client::sso-button />

{{-- Auto-redirect diam-diam jika sudah login di SSO --}}
<x-sso-client::auto-redirect />
```

Opsional: jika ingin halaman login client **dilewati sepenuhnya** saat user sudah punya sesi SSO, tambahkan middleware bawaan pada route login:

```php
use Demak\SsoClient\Http\Middleware\RedirectIfSsoSession;

Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware(RedirectIfSsoSession::class)
    ->name('login');
```

---

### Langkah 7 — Bersihkan Cache Konfigurasi & Route

```bash
php artisan config:clear
php artisan route:clear
php artisan route:list | grep sso
```

Pastikan muncul 3 route: `sso.redirect`, `sso.auto-login`, `sso.callback`.

---

### Langkah 8 — Uji Coba

1. Login ke `sso-pbj.test` → mendarat di `/apps`.
2. Aplikasi baru harus **muncul sebagai kartu** di dashboard.
3. Klik **"Buka Aplikasi"** → tab baru terbuka di aplikasi client dalam keadaan **sudah login** (tanpa password & tanpa consent).
4. Klik **Logout** di aplikasi client → harus kembali ke `/apps` SSO.

✅ **Aplikasi client baru selesai terintegrasi.**

---

### Referensi Cepat Variabel `.env` Client

| Variabel | Wajib | Default | Keterangan |
|----------|:-----:|---------|------------|
| `SSO_BASE_URL` | ✅ | — | URL server SSO (mis. `http://sso-pbj.test`) |
| `SSO_CLIENT_ID` | ✅ | — | `passport_client_id` dari Admin SSO |
| `SSO_CLIENT_SECRET` | ❌ | — | Kosongkan (PKCE public client) |
| `SSO_REDIRECT_URI` | ✅ | — | Mis. `http://app.test/sso/callback` |
| `SSO_SCOPE` | ❌ | `user:read` | Scope OAuth yang diminta |
| `SSO_USER_API` | ❌ | `/api/user` | Endpoint profil user di SSO |
| `SSO_TIMEOUT` | ❌ | `15` | Timeout HTTP ke SSO (detik) |
| `SSO_LOGIN_ROUTE` | ❌ | `login` | Nama route login lokal (fallback) |
| `SSO_HOME_ROUTE` | ❌ | `home` | Nama route home setelah login |
| `SSO_USER_MODEL` | ❌ | `App\Models\User` | Model user lokal |
| `SSO_RESOLVER` | ❌ | `DefaultUserResolver` | Class resolver pencocokan user |
| `SSO_CLIENT_ROUTES` | ❌ | `true` | `false` jika aplikasi punya controller SSO sendiri |

---

## 1. Instalasi & Setup Server SSO
- Composer
- Database (MySQL/PostgreSQL)
- Web server (Nginx/Apache) atau Laravel Valet/Laragon

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repo-url> sso-pbj
cd sso-pbj

# 2. Install dependency
composer install

# 3. Copy environment
cp .env.example .env

# 4. Setup database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sso_pbj
DB_USERNAME=root
DB_PASSWORD=

# 5. Generate key & migrate
php artisan key:generate
php artisan migrate --seed

# 6. Install Passport (generate encryption keys + client personal access)
php artisan passport:install
# Catat "Personal Access Client" ID & Secret (untuk testing API jika perlu)

# 7. Buat symbolic link storage (jika perlu)
php artisan storage:link

# 8. Jalankan server
php artisan serve --host=sso-pbj.test --port=80
# Atau konfigurasi virtual host di Laragon/Nginx ke folder public/
```

### Konfigurasi Penting di `.env` Server SSO

```env
APP_NAME="SSO Demak"
APP_URL=http://sso-pbj.test

# Passport
PASSPORT_PRIVATE_KEY_PATH=storage/oauth-private.key
PASSPORT_PUBLIC_KEY_PATH=storage/oauth-public.key

# Session (penting untuk cross-domain jika pakai subdomain)
SESSION_DOMAIN=.test
SESSION_SECURE_COOKIE=false
SANCTUM_STATEFUL_DOMAINS=sso-pbj.test,rup-app.test
```

---

## 2. Pendaftaran Aplikasi Client di SSO

Setelah server SSO berjalan, daftarkan setiap aplikasi client:

1. Buka `http://sso-pbj.test/login` → login sebagai **super-admin** / admin.
2. Akses menu **Admin → Aplikasi** atau `/apps`.
3. Klik **Tambah Aplikasi** dan isi:
   - **Nama Aplikasi**: Contoh: `Aplikasi RUP`
   - **Slug**: `rup` (unik, tanpa spasi, lowercase)
   - **Redirect URI**: `http://rup-app.test/sso/callback` *(wajib sama persis dengan client)*
   - **Status**: ✅ Aktif
4. Simpan. Sistem akan generate **Client ID** otomatis.
5. **Salin Client ID** → akan dipakai di `.env` aplikasi client.

> **Catatan:** Karena `Client::skipsAuthorization()` sudah di-override jadi `return $this->isActive()`, aplikasi yang statusnya **Aktif** akan melewati layar consent sepenuhnya.

---

## 3. Integrasi Aplikasi Client Baru (via Composer VCS)

Paket integrasi client sudah dipisah ke repository terpisah: **`pemkab-demak/sso-client`** (sudah diupload ke GitHub).

### A. Tambahkan Repository VCS ke `composer.json` Client

Buka `composer.json` di aplikasi client baru (misal `e-monev-app`), tambahkan blok `repositories` dan require package:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/arissaturrohman/sso-pbj.git"
        }
    ],
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0|^12.0",
        "pemkab-demak/sso-client": "^1.0"
    }
}
```

> **Ganti URL** di atas dengan URL repository GitHub Anda yang sebenarnya.

### B. Install Package

```bash
cd /path/ke/aplikasi-client-baru
composer update pemkab-demak/sso-client
```

Package akan otomatis:
- Mendaftarkan Service Provider (`SsoClientServiceProvider`)
- Mem-publish config `config/sso-client.php`
- Mendaftarkan route `/sso/redirect`, `/sso/auto-login`, `/sso/callback`
- Menyediakan Blade components: `<x-sso-client::sso-button />` & `<x-sso-client::auto-redirect />`
- Menyediakan Event `SsoLoginSucceeded` untuk hook pasca-login

---

## 4. Konfigurasi Environment Client

Tambahkan variabel berikut ke `.env` aplikasi client:

```env
# ===== SSO Client Config =====
SSO_BASE_URL=http://sso-pbj.test
SSO_CLIENT_ID=1                    # ← Client ID dari pendaftaran di SSO (Langkah 2)
SSO_CLIENT_SECRET=                 # Kosongkan (PKCE public client)
SSO_REDIRECT_URI=http://nama-aplikasi-client.test/sso/callback

# Opsional: nonaktifkan route otomatis paket jika sudah punya controller sendiri
# SSO_CLIENT_ROUTES=false

# Opsional: custom resolver jika skema user beda
# SSO_RESOLVER=App\Sso\CustomResolver
```

**Penjelasan:**
| Variabel | Wajib? | Keterangan |
|----------|--------|------------|
| `SSO_BASE_URL` | ✅ | URL server SSO pusat |
| `SSO_CLIENT_ID` | ✅ | ID dari tabel `oauth_clients` / pendaftaran di Admin SSO |
| `SSO_CLIENT_SECRET` | ❌ | Dikosongkan karena pakai **PKCE** (public client) |
| `SSO_REDIRECT_URI` | ✅ | Harus **sama persis** dengan Redirect URI di pendaftaran SSO |
| `SSO_CLIENT_ROUTES` | ❌ | `false` jika client sudah punya `SsoController` sendiri (seperti rup-app) |
| `SSO_RESOLVER` | ❌ | Class custom resolver (lihat bagian 6) |

---

## 5. Memasang Komponen UI di Halaman Login

Buka `resources/views/auth/login.blade.php` aplikasi client, tambahkan di area form login:

```blade
<form method="POST" action="{{ route('login') }}">
    {{-- Form login manual biasa (email/password) --}}
    @csrf
    <!-- ... field email, password, remember, dll ... -->
    <button type="submit" class="btn btn-primary w-100">Login Manual</button>
</form>

<hr class="my-3">

{{-- 🔑 Tombol Login SSO --}}
<x-sso-client::sso-button />

{{-- 🔄 Auto-Redirect Diam-Diam (RECOMMENDED) --}}
<x-sso-client::auto-redirect />
```

### Penjelasan Komponen

| Komponen | Fungsi |
|----------|--------|
| `<x-sso-client::sso-button />` | Tombol "Login via SSO Demak" → redirect ke `/sso/redirect` → SSO login → callback |
| `<x-sso-client::auto-redirect />` | Overlay otomatis 1,5 detik cek session SSO. Jika sudah login di SSO → langsung redirect ke client. Tombol "Batal" tersedia untuk fallback login manual. |

> **Best Practice:** Pasang **keduanya**. Auto-redirect untuk UX mulus (user sudah login SSO), tombol manual sebagai fallback eksplisit.

---

## 6. Kustomisasi Resolver User (Jika Skema Database Berbeda)

Default resolver (`DefaultUserResolver`) mencocokkan user berdasarkan:
1. **NIP** (prioritas utama) → exact match
2. **Email** (fallback) → case-insensitive
3. **Status aktif** → `$user->status` harus `'active'`, `1`, atau `true`

Jika aplikasi client memiliki skema berbeda (misal: pakai `nik`, `employee_id`, atau tabel terpisah), buat resolver kustom:

### Langkah:

```bash
# 1. Publish template resolver
php artisan vendor:publish --tag=sso-client-resolver
# Output: app/Sso/CustomResolver.php
```

### 2. Edit `app/Sso/CustomResolver.php`

```php
<?php

namespace App\Sso;

use Demak\SsoClient\Contracts\SsoUserResolver;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomResolver implements SsoUserResolver
{
    public function resolve(array $profile): ?Authenticatable
    {
        // $profile berisi data dari SSO: ['id', 'nip', 'email', 'name', 'roles', ...]
        
        // Contoh: cocokkan via employee_id
        if (!empty($profile['employee_id'])) {
            $user = \App\Models\User::where('employee_id', $profile['employee_id'])->first();
        } 
        // Fallback email
        elseif (!empty($profile['email'])) {
            $email = mb_strtolower(trim($profile['email']));
            $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$email])->first();
        } else {
            return null;
        }

        if (!$user) {
            return null;
        }

        // Cek status aktif (sesuaikan dengan enum/kolom aplikasi Anda)
        $status = $user->status ?? null;
        $isActive = in_array($status, ['active', 'aktif', 1, true], true);

        return $isActive ? $user : null;
    }
}
```

### 3. Daftarkan di `.env` client

```env
SSO_RESOLVER=App\Sso\CustomResolver
```

---

## 7. Alur Kerja Lengkap (End-to-End)

### Skenario A: Login Pertama Kali via Client (rup-app.test/login)
1. User buka `rup-app.test/login`
2. `<x-sso-client::auto-redirect />` deteksi belum login SSO → redirect ke `/sso/redirect`
3. `/sso/redirect` generate PKCE + state → redirect ke `sso-pbj.test/oauth/authorize?...`
4. User dihadapkan halaman login SSO (`sso-pbj.test/login`)
5. User login → **dipaksa ke `/apps`** (Dashboard SSO)
6. User klik "Buka Aplikasi" RUP → tab baru ke `rup-app.test/sso/redirect`
7. Karena session SSO aktif + `skipAuthorization` → redirect balik ke `rup-app.test/sso/callback?code=...`
8. Client tukar code → access token → ambil profile user → `Auth::login($user)` → redirect ke dashboard RUP
9. **Selesai:** User login di RUP tanpa ketik password.

### Skenario B: User Sudah Login SSO, Buka Client Baru (e-monev.test)
1. User sudah login di SSO (tab `/apps` terbuka)
2. User klik "Buka Aplikasi" E-Monev → tab baru ke `e-monev.test/sso/redirect`
3. Langsung callback → login otomatis → dashboard E-Monev
4. **Tidak perlu login ulang, tidak perlu consent.**

### Skenario C: Akses Langsung URL Client (Auto-Redirect)
1. User ketik `rup-app.test` di browser baru (belum login local)
2. Middleware `RedirectIfSsoSession` deteksi guest → render login view
3. `<x-sso-client::auto-redirect />` jalan → overlay 1,5 dtk → redirect ke SSO
4. SSO session aktif → skip login → redirect balik ke client → login otomatis
5. User masuk dashboard RUP **tanpa klik apa pun**.

### Skenario D: Logout
1. User klik Logout di RUP
2. `AuthController@logout()` → `Auth::logout()` → session destroy
3. Redirect ke `rtrim(config('services.sso.base_url'), '/') . '/apps'` → halaman `/apps` SSO
4. User bisa klik aplikasi lain atau login lagi.

---

## 8. Troubleshooting

### ❌ "Invalid redirect URI" / "The redirect uri is not valid"
- Pastikan `SSO_REDIRECT_URI` di `.env` client **sama persis** (termasuk trailing slash atau tidak) dengan Redirect URI yang didaftarkan di Admin SSO.
- Cek tabel `oauth_clients` kolom `redirect` di database SSO.

### ❌ "Invalid state" / "CSRF token mismatch" di callback
- PKCE `state` disimpan di session client. Pastikan session driver sama (file/redis/database) dan domain cookie konsisten.
- Jika pakai subdomain berbeda, set `SESSION_DOMAIN=.test` di kedua aplikasi.

### ❌ Auto-redirect tidak jalan / stuck di overlay
- Cek console browser: error JS? Pastikan `<x-sso-client::auto-redirect />` dirender di dalam `<body>`.
- Pastikan route `sso.redirect` tersedia (`php artisan route:list | grep sso.redirect`).
- Jika SSO down, tombol "Batal" akan set `?sso=off` dan hentikan redirect loop.

### ❌ User tidak ketemu / "Unauthorized" setelah callback
- Cek resolver: default pakai `nip` → `email`. Pastikan user di SSO dan client punya data yang cocok.
- Cek kolom `status` user di client: harus `active` / `1` / `true`.
- Enable log: `Log::channel('sso')->info('SSO Profile', $profile)` di resolver untuk debug.

### ❌ Logout di client tidak ke `/apps` SSO
- Pastikan `AuthController@logout()` di client memakai redirect ke `config('services.sso.base_url') . '/apps'`.
- Atau pakai middleware/event `SsoLoginSucceeded` untuk custom logic.

---

## 📦 Struktur Package `pemkab-demak/sso-client`

```
sso-client/
├── composer.json
├── config/sso-client.php
├── routes/web.php
├── src/
│   ├── Contracts/SsoUserResolver.php
│   ├── Resolvers/DefaultUserResolver.php
│   ├── Http/Controllers/SsoController.php
│   ├── Http/Middleware/RedirectIfSsoSession.php
│   ├── Support/SsoConfig.php
│   ├── Events/SsoLoginSucceeded.php
│   └── SsoClientServiceProvider.php
├── resources/views/components/
│   ├── sso-button.blade.php
│   └── sso-auto-redirect.blade.php
├── stubs/CustomResolver.stub
└── README.md           ← Dokumentasi integrasi client (file ini juga ada di package)
```

---

## 🔐 Keamanan

- **PKCE (RFC 7636)**: `code_verifier` & `code_challenge` digenerate per request di client, mencegah interception code.
- **Public Client**: `client_secret` tidak dipakai/dikirim dari client (dikosongkan di `.env`).
- **Skip Consent Hanya untuk Aktif**: `Client::skipsAuthorization()` hanya return `true` jika `$this->isActive()`. Aplikasi non-aktif tetap diminta consent (defense in depth).
- **State Parameter**: Validasi CSRF via `state` parameter OAuth2 standar.
- **Session Hijacking Protection**: `RedirectIfSsoSession` middleware cek `Auth::check()` sebelum redirect.

---

## 📝 Changelog

| Versi | Tanggal | Perubahan |
|-------|---------|-----------|
| 1.0.0 | 2026-10-12 | Rilis awal: SSO server + client package, auto-consent, auto-redirect, logout terpusat, resolver kustom |

---

## 🤝 Kontribusi & Lisensi

- **Lisensi**: MIT (internal Pemkab Demak)
- **Maintainer**: Tim TI Pemkab Demak
- **Repository SSO Server**: `sso-pbj` (private)
- **Repository Client Package**: `https://github.com/NangAhmadd/sso-client` (VCS)

---

## 📞 Bantuan

Jika mengalami masalah integrasi:
1. Cek log: `storage/logs/laravel.log` (filter `sso` atau `SSO`)
2. Pastikan versi Laravel client kompatibel (`^11.0|^12.0`)
3. Hubungi tim TI Pemkab Demak