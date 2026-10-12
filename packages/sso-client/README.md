# pemkab-demak/sso-client

Klien SSO OAuth2 (Authorization Code + PKCE) untuk aplikasi-aplikasi Pemkab Demak.
Alurnya identik dengan yang sudah teruji di **rup-app**: client men-generate state + PKCE
di session-nya sendiri, server SSO (`sso-pbj`) skip-consent untuk client aktif, callback
menukar code → ambil profil `/api/user` → cocokkan user lokal → `Auth::login()`.

## 1. Pasang di aplikasi baru (5 menit)

```bash
composer require pemkab-demak/sso-client
```

Tambahkan ke `.env` aplikasi (minta Client ID ke admin SSO setelah daftarkan aplikasi
di `sso.apps` → Kelola Aplikasi):

```env
SSO_BASE_URL=http://sso-pbj.test
SSO_CLIENT_ID=<client-id-dari-sso>
SSO_CLIENT_SECRET=<boleh-kosong-untuk-public-client>
SSO_REDIRECT_URI=http://aplikasi-baru.test/sso/callback
```

Selesai — route ini langsung aktif tanpa controller tambahan:

| Method | URL | Nama | Fungsi |
|---|---|---|---|
| GET | `/sso/redirect` | `sso.redirect` | Mulai login SSO (tombol Login) |
| GET | `/sso/auto-login` | `sso.auto-login` | Alias, dipakai auto-redirect |
| GET | `/sso/callback` | `sso.callback` | Terima code, login lokal |

Pasang dua komponen Blade di halaman login:

```blade
{{-- tombol SSO --}}
<x-sso-client::sso-button />

{{-- overlay auto-redirect ±1,5 dtk + tombol Batal (fallback manual) --}}
<x-sso-client::auto-redirect />
```

Login dengan SSO berhasil → event `Demak\SsoClient\Events\SsoLoginSucceeded`
dipicu (pasang listener untuk `last_login_at` / log aktivitas kustom).
Gagal → kembali ke route `login` dengan session `error` (pesan ramah).

## 2. Kalau skema user-nya berbeda

Buat resolver sendiri dari stub, lalu arahkan env:

```bash
php artisan vendor:publish --tag=sso-client-resolver
```

```env
SSO_RESOLVER=App\Sso\CustomResolver
```

Contoh ada di `stubs/CustomResolver.stub`.

## 3. Pengaturan opsional (.env)

| Key | Default | Keterangan |
|---|---|---|
| `SSO_SCOPE` | `user:read` | Scope OAuth |
| `SSO_USER_API` | `/api/user` | Endpoint profil di server SSO |
| `SSO_TIMEOUT` | `15` | Timeout HTTP (detik) |
| `SSO_LOGIN_ROUTE` | `login` | Route login manual (fallback) |
| `SSO_HOME_ROUTE` | `home` | Tujuan setelah login SSO sukses |
| `SSO_USER_MODEL` | `App\Models\User` | Model user lokal |
| `SSO_CLIENT_ROUTES` | `true` | `false` = pakai route/controller sendiri |

Konfigurasi lengkap bisa di-publish: `php artisan vendor:publish --tag=sso-client-config`.

## 4. Migrasi rup-app (sudah jalan, tidak wajib)

`rup-app` tetap memakai `SsoController`-nya sendiri — tidak perlu diubah, karena
package membaca fallback `config('services.sso.*')` yang sama. Kalau suatu saat mau
beralih: set `SSO_HOME_ROUTE=admin.dashboard` di `.env` rup-app (tanpa buka file
lain), hapus route `sso.*` + `SsoController` lama, install package ini.
Catat: log aktivitas `LogsActivity` dan overlay kustom rup-app ikut terhapus —
pindahkan dulu ke listener `SsoLoginSucceeded` bila masih dibutuhkan.

## 5. Mendaftarkan aplikasi baru di server SSO

1. Login `sso-pbj.test` → **Aplikasi Saya** (`/apps`).
2. **Kelola Aplikasi** → Tambah: nama, slug, `redirect_uri` = `https://aplikasi-baru.test/sso/callback`, status aktif.
3. Salin **Client ID** yang dihasilkan ke `.env` aplikasi baru.
4. Aplikasi otomatis muncul sebagai kartu di `/apps`; tombol **Buka Aplikasi**
   (`target=_blank`) langsung login tanpa password/consent.
