# Panduan Integrasi Klien SSO (`pemkab-demak/sso-client`)

Repositori ini adalah paket Composer privat/publik untuk mengintegrasikan aplikasi client Pemkab Demak dengan server pusat SSO (**sso-pbj**). Paket ini menggunakan alur **OAuth2 Authorization Code + PKCE** sehingga aman, tidak membutuhkan *client secret* yang diekspos di aplikasi publik, dan mendukung fitur **Single Sign-On (SSO)** serta **Auto-Redirect diam-diam**.

---

## 1. Menghubungkan Repositori Git ke Aplikasi Client

Karena repositori ini sudah diunggah ke GitHub, aplikasi client Anda harus diberi tahu cara mengunduhnya.

Buka file `composer.json` di aplikasi client Anda (misalnya `rup-app` atau aplikasi lainnya), lalu tambahkan blok `"repositories"` di bagian atas/bawah, dan tambahkan `"pemkab-demak/sso-client": "^1.0"` di dalam `"require"`:

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
*(Catatan: Ganti URL di atas dengan URL repository GitHub Anda yang sebenarnya).*

Setelah itu, jalankan perintah ini di terminal root aplikasi client Anda:
```bash
composer update pemkab-demak/sso-client
```

---

## 2. Konfigurasi Environment (`.env`)

Tambahkan baris berikut ke file `.env` di aplikasi client Anda:

```env
SSO_BASE_URL=http://sso-pbj.test
SSO_CLIENT_ID=<dapatkan-client-id-dari-dashboard-sso>
SSO_CLIENT_SECRET=
SSO_REDIRECT_URI=http://nama-aplikasi-client.test/sso/callback
```
* **SSO_BASE_URL**: Alamat server pusat SSO Anda (misal `http://sso-pbj.test` atau URL production).
* **SSO_CLIENT_ID**: ID unik yang didapat setelah mendaftarkan aplikasi di menu Admin SSO (`/apps`).
* **SSO_REDIRECT_URI**: URL callback aplikasi Anda (wajib sama persis dengan yang didaftarkan di server SSO).

---

## 3. Pendaftaran Aplikasi di Server SSO (`sso-pbj`)

Sebelum aplikasi client dapat mengenali user, aplikasi tersebut harus didaftarkan di server pusat SSO:
1. Login ke server pusat SSO (`sso-pbj.test/login`).
2. Masuk ke menu **Aplikasi Saya** (`/apps`) atau **Admin > Aplikasi**.
3. Klik **Tambah Aplikasi** / daftarkan aplikasi baru:
   - **Nama Aplikasi**: Nama yang mudah dibaca (mis. *Aplikasi E-Monev*).
   - **Slug**: Nama singkat tanpa spasi (mis. *e-monev*).
   - **Redirect URI**: `http://nama-aplikasi-client.test/sso/callback`.
4. Simpan. Server SSO akan men-generate **Client ID** otomatis.
5. Salin Client ID tersebut dan masukkan ke `.env` aplikasi client (Langkah No. 2).

*Catatan: Karena sistem sudah diset untuk `skipsAuthorization`, user yang membuka aplikasi client **tidak akan pernah lagi** melihat halaman konfirmasi "Izinkan akses".*

---

## 4. Memasang Tombol & Auto-Redirect di Halaman Login Client

Paket ini sudah menyediakan komponen Blade siap pakai. Buka file tampilan login aplikasi client Anda (biasanya `resources/views/auth/login.blade.php`), lalu pasang:

### A. Tombol Login SSO
```blade
<x-sso-client::sso-button />
```

### B. Auto-Redirect Diam-diam (Opsional tapi Direkomendasikan)
Komponen ini akan otomatis mengecek sesi SSO. Jika user sudah login di SSO, halaman login client akan menampilkan animasi loading singkat (±1,5 detik) lalu langsung melemparkan user ke dalam aplikasi **tanpa perlu klik tombol apa pun**. Di dalamnya sudah tersedia tombol **"Batal — login manual"** sebagai *fallback* jika server SSO bermasalah.

```blade
<x-sso-client::auto-redirect />
```

Contoh penempatan di `login.blade.php`:
```html
<form method="POST" action="{{ route('login') }}">
    <!-- Form login manual biasa -->
</form>

<hr class="my-3">

{{-- Tombol dan Auto-Redirect SSO --}}
<x-sso-client::sso-button />
<x-sso-client::auto-redirect />
```

---

## 5. Kustomisasi Resolver User (Jika Skema Database Berbeda)

Secara default, package ini mencocokkan user berdasarkan **NIP** (prioritas pertama) atau **Email** (case-insensitive) pada model `App\Models\User`, dan memastikan kolom `status` bernilai aktif (`active`, `1`, atau `true`).

Jika aplikasi Anda menggunakan struktur tabel user yang berbeda, Anda bisa membuat resolver kustom:
1. Publish template resolver bawaan:
   ```bash
   php artisan vendor:publish --tag=sso-client-resolver
   ```
   Perintah ini akan membuat file di `app/Sso/CustomResolver.php`.
2. Sesuaikan logika pencarian user di dalam method `resolve(array $profile)` di file tersebut.
3. Daftarkan di `.env` aplikasi client:
   ```env
   SSO_RESOLVER=App\Sso\CustomResolver
   ```

---

## 6. Route Otomatis yang Disediakan Paket

Paket ini secara otomatis mendaftarkan 3 route dengan prefix `/sso`:
* `GET /sso/redirect` (Nama: `sso.redirect`) — Mengarahkan user ke halaman login SSO.
* `GET /sso/auto-login` (Nama: `sso.auto-login`) — Alias dari redirect (dipakai oleh komponen auto-redirect).
* `GET /sso/callback` (Nama: `sso.callback`) — Menerima kode otorisasi, menukar dengan token, dan membuat sesi login lokal.

*(Jika aplikasi Anda sudah memiliki controller SSO sendiri — seperti rup-app — Anda bisa menonaktifkan route otomatis ini dengan memberi `SSO_CLIENT_ROUTES=false` di `.env`)*.
