# Panduan Integrasi SSO Client (`pemkab-demak/sso-client`)

Panduan ini menjelaskan cara memasang package Composer
`pemkab-demak/sso-client` pada aplikasi Laravel, misalnya **Buku Tamu**
atau aplikasi client lainnya, agar autentikasi dapat menggunakan server
pusat **SSO-PBJ**.

Package menyediakan route SSO otomatis dengan prefix `/sso` dan
menggunakan alur OAuth2 Authorization Code + PKCE sesuai implementasi
package. Pada konfigurasi client yang menggunakan PKCE,
`SSO_CLIENT_SECRET` tidak perlu diisi kecuali kode package atau
konfigurasi server Anda secara eksplisit mensyaratkannya.

> **Penting:** Sesuaikan konfigurasi dengan versi package yang
> benar-benar terpasang. Jangan menaruh token GitHub atau kredensial
> rahasia di README, `composer.json`, atau repository Git.

------------------------------------------------------------------------

## 1. Prasyarat

Pastikan:

-   Aplikasi client menggunakan Laravel yang didukung package.
-   Server SSO-PBJ sudah berjalan.
-   Aplikasi client sudah terdaftar di server SSO-PBJ.
-   Anda memiliki akses ke repository GitHub
    `arissaturrohman/sso-client`.
-   URL callback di SSO-PBJ sama persis dengan callback package di
    aplikasi client.
-   Database aplikasi client memiliki kolom `nip` jika akun dicocokkan
    berdasarkan NIP.

Contoh URL pengembangan lokal:

  Komponen             URL
  -------------------- --------------------------------------
  Server SSO-PBJ       `http://sso-pbj.test`
  Aplikasi Buku Tamu   `http://buku-tamu.test`
  Callback Buku Tamu   `http://buku-tamu.test/sso/callback`

Pada server produksi, gunakan domain HTTPS yang sebenarnya.

## 2. Berikan akses Composer ke repository

Repository package:

`https://github.com/arissaturrohman/sso-client`

Jika repository privat, pastikan akun GitHub Anda memiliki akses.
Apabila Composer meminta autentikasi GitHub, konfigurasi token secara
lokal dengan izin minimum yang dibutuhkan. Jangan commit token ke
repository dan jangan membagikannya melalui chat.

Contoh perintah konfigurasi lokal:

``` bash
composer config --global --auth github-oauth.github.com TOKEN_GITHUB_ANDA
```

Ganti `TOKEN_GITHUB_ANDA` dengan token yang valid. Hindari menyimpan
token dalam file proyek. Jika token pernah terpublikasi, cabut token
tersebut dan buat token pengganti.

## 3. Tambahkan repository package di `composer.json`

Buka `composer.json` di direktori utama aplikasi client. Tambahkan
repository VCS berikut ke key `repositories` di tingkat teratas:

``` json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/arissaturrohman/sso-client.git"
    }
]
```

Jika `repositories` sudah ada, tambahkan objek repository ke array yang
sudah ada; jangan membuat key `repositories` ganda.

Pastikan package tercantum pada bagian `require`:

``` json
"pemkab-demak/sso-client": "^1.0"
```

Jangan menimpa seluruh `composer.json` hanya dengan contoh di atas.
Pertahankan dependensi dan konfigurasi proyek yang sudah ada.

## 4. Instal package dengan Composer

Jalankan dari direktori aplikasi client.

Jika package **belum** tercantum di `composer.json`, gunakan:

``` bash
composer require pemkab-demak/sso-client:^1.0
```

Jika package sudah tercantum pada `composer.json` dan Anda ingin
memperbarui versi yang terkunci:

``` bash
composer update pemkab-demak/sso-client
```

Setelah instalasi selesai, periksa route yang didaftarkan:

``` bash
php artisan route:list
```

Package yang digunakan pada aplikasi Buku Tamu saat ini mendaftarkan
route berikut:

  -------------------------------------------------------------------------
  Method            URI                Nama route         Fungsi
  ----------------- ------------------ ------------------ -----------------
  GET               `sso/redirect`     `sso.redirect`     Mengarahkan
                                                          pengguna ke SSO

  GET               `sso/auto-login`   `sso.auto-login`   Alias redirect
                                                          untuk alur
                                                          auto-login

  GET               `sso/callback`     `sso.callback`     Menangani
                                                          callback
                                                          autentikasi
  -------------------------------------------------------------------------

Jika route tersebut tidak muncul, periksa hasil instalasi Composer,
konfigurasi service provider, dan versi package yang terpasang.

## 5. Daftarkan aplikasi di server SSO-PBJ

1.  Login ke server SSO-PBJ.
2.  Buka menu pengelolaan aplikasi yang sesuai dengan hak akses Anda,
    misalnya **Admin \> Aplikasi**.
3.  Tambahkan aplikasi client atau buka aplikasi yang sudah terdaftar.
4.  Isi nama aplikasi, slug, deskripsi, dan status sesuai kebutuhan.
5.  Isi **Redirect URI** dengan callback yang terdaftar di aplikasi
    client.
6.  Simpan perubahan dan salin Client ID yang diterbitkan server SSO.

### Contoh untuk Buku Tamu

Pada aplikasi Buku Tamu, hasil `php artisan route:list` menunjukkan
callback package adalah:

``` text
http://buku-tamu.test/sso/callback
```

Karena itu, field `redirect_uri` di SSO-PBJ harus berisi URL yang sama
persis:

``` text
http://buku-tamu.test/sso/callback
```

**Jangan menggunakan** `http://buku-tamu.test/auth/callback` untuk
konfigurasi package saat ini, karena route tersebut tidak muncul pada
`route:list` Buku Tamu yang diperiksa.

URL callback harus sama persis, termasuk protokol (`http`/`https`),
domain, port jika ada, path, dan trailing slash.

## 6. Konfigurasikan `.env` aplikasi client

Tambahkan atau sesuaikan konfigurasi berikut pada `.env` aplikasi
client:

``` dotenv
SSO_BASE_URL=http://sso-pbj.test
SSO_CLIENT_ID=ISI_CLIENT_ID_DARI_SSO
SSO_CLIENT_SECRET=
SSO_REDIRECT_URI=http://buku-tamu.test/sso/callback
```

Keterangan:

-   `SSO_BASE_URL`: alamat server SSO-PBJ.
-   `SSO_CLIENT_ID`: Client ID yang didapat dari pendaftaran aplikasi di
    SSO-PBJ.
-   `SSO_CLIENT_SECRET`: kosongkan jika alur package yang terpasang
    menggunakan PKCE dan tidak membutuhkan client secret.
-   `SSO_REDIRECT_URI`: callback yang harus sama persis dengan
    `redirect_uri` di SSO-PBJ.

Untuk aplikasi lain, ganti domain `buku-tamu.test` dengan domain
aplikasi tersebut dan daftarkan callback yang sesuai di SSO-PBJ.
Pastikan nama variabel cocok dengan yang benar-benar dibaca oleh versi
package yang terpasang.

Setelah mengubah `.env`, jalankan:

``` bash
php artisan config:clear
```

## 7. Pasang tombol login SSO

Pada halaman login client, tambahkan komponen tombol SSO yang tersedia
dari package:

``` blade
<x-sso-client::sso-button />
```

Letakkan di lokasi yang sesuai pada
`resources/views/auth/login.blade.php` atau view login yang digunakan
aplikasi Anda.

Contoh:

``` blade
<form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Form login lokal yang sudah ada --}}
</form>

<hr class="my-3">

<x-sso-client::sso-button />
```

Sesuaikan contoh tersebut dengan form login yang sudah ada. Jangan
mengganti seluruh halaman login hanya untuk menambahkan tombol SSO.

## 8. Auto-redirect SSO (opsional)

Package juga menyediakan komponen auto-redirect. Pada versi package yang
file view-nya bernama `sso-auto-redirect.blade.php`, gunakan:

``` blade
<x-sso-client::sso-auto-redirect />
```

**Jangan menggunakan** `<x-sso-client::auto-redirect />` kecuali versi
package yang terpasang memang menyediakan view dengan nama tersebut.
Nama komponen harus sesuai dengan file Blade yang tersedia di package.

Tempatkan komponen ini pada halaman login jika memang ingin mengaktifkan
perilaku auto-redirect. Uji dengan teliti agar pengguna tetap dapat
membatalkan proses atau menggunakan login manual sesuai implementasi
komponen.

## 9. Pencocokan akun lokal melalui resolver

Pada konfigurasi resolver yang digunakan untuk integrasi Buku Tamu saat
ini, pencocokan dilakukan dengan ketentuan:

1.  Membaca model dari `sso-client.user_model`, dengan default
    `App\Models\User`.
2.  Jika profil SSO memiliki NIP, resolver mencari pengguna lokal
    berdasarkan kolom `nip`.
3.  Jika NIP tidak tersedia pada profil, resolver mencari berdasarkan
    email tanpa membedakan huruf besar/kecil.
4.  Jika pengguna tidak ditemukan, login ditolak.
5.  Status akun lokal harus termasuk status aktif yang diterima
    resolver, termasuk `aktif`.

Perhatikan bahwa pada implementasi ini, pencarian email merupakan
alternatif ketika NIP pada profil kosong. Jika NIP ada tetapi tidak
cocok dengan akun lokal, resolver tidak otomatis mencoba email.

Pastikan tabel `users` memiliki kolom `nip` dan nilai NIP lokal sesuai
dengan profil yang dikirim SSO. Kolom `username` tetap dapat
dipertahankan untuk login lokal; kolom `nip` digunakan terpisah untuk
pencocokan SSO.

Jika struktur database aplikasi berbeda, sesuaikan resolver di source
package atau gunakan mekanisme resolver kustom yang memang tersedia pada
versi package tersebut.

> Perintah publikasi resolver seperti
> `php artisan vendor:publish --tag=sso-client-resolver` hanya boleh
> digunakan jika tag tersebut benar-benar terdaftar pada package yang
> dipasang. Periksa tag yang tersedia terlebih dahulu:
>
> ``` bash
> php artisan vendor:publish --list
> ```

## 10. Periksa alur login

Alur umumnya:

1.  Pengguna menekan tombol **Login dengan SSO** atau memulai
    auto-redirect.
2.  Aplikasi client mengarahkan browser ke server SSO-PBJ.
3.  Pengguna login pada SSO-PBJ jika belum memiliki sesi aktif.
4.  SSO mengarahkan browser kembali ke callback aplikasi client.
5.  Package memproses callback dan menukar kode otorisasi sesuai
    implementasinya.
6.  Resolver mencari akun lokal.
7.  Jika akun ditemukan dan aktif, pengguna masuk ke aplikasi client.

Jika aplikasi sudah memiliki controller SSO sendiri dan Anda ingin
menonaktifkan route otomatis package, gunakan `SSO_CLIENT_ROUTES=false`
hanya jika opsi tersebut memang didukung oleh versi package yang
terpasang. Setelah mengubahnya, jalankan `php artisan route:list` untuk
memastikan hasilnya.

## 11. Pemecahan masalah

### Error `invalid_client` atau `Client authentication failed`

Periksa Client ID, metode autentikasi client yang digunakan server,
konfigurasi PKCE, serta nama variabel `.env` yang benar-benar dibaca
package. Jangan mengisi Client Secret secara sembarang jika alur yang
digunakan memang tidak memerlukannya.

Setelah mengubah konfigurasi:

``` bash
php artisan config:clear
```

### Error `redirect_uri` tidak cocok

1.  Jalankan `php artisan route:list` pada aplikasi client.
2.  Temukan URI callback yang benar-benar terdaftar.
3.  Samakan `SSO_REDIRECT_URI` di `.env` dengan `redirect_uri` pada
    SSO-PBJ.
4.  Pastikan kedua URL sama persis.

Untuk Buku Tamu pada konfigurasi saat ini, callback yang terdaftar
adalah:

``` text
http://buku-tamu.test/sso/callback
```

### Error kolom `nip` tidak ditemukan

Pastikan migration untuk menambahkan kolom `nip` sudah dijalankan pada
database yang benar. Periksa status migration:

``` bash
php artisan migrate:status
```

Tinjau dan backup database sebelum menjalankan migration pada server
produksi.

### Pesan akun tidak ditemukan atau tidak aktif

Periksa apakah:

-   NIP pada profil SSO cocok dengan kolom `users.nip`.
-   Jika NIP tidak dikirim, email pada profil SSO cocok dengan email
    akun lokal.
-   Akun lokal ada dan statusnya aktif sesuai aturan resolver.
-   Model pengguna dan nama kolom sesuai konfigurasi package.

### Komponen Blade tidak ditemukan

Periksa nama file view komponen pada package yang terpasang. Nama
komponen Blade ber-prefix harus mengikuti nama file sebenarnya. Untuk
versi yang memiliki file `components/sso-auto-redirect.blade.php`,
gunakan `<x-sso-client::sso-auto-redirect />`.

### Composer meminta autentikasi GitHub

Pastikan repository privat dapat diakses oleh akun GitHub Anda dan
kredensial Composer valid. Jangan memasukkan token ke file proyek atau
membagikannya.

## 12. Deployment ke server produksi

Sebelum deploy:

-   Pastikan `composer.json` dan `composer.lock` ikut tersimpan di Git.
-   Pastikan `.env` dan token tidak ikut ter-commit.
-   Daftarkan callback domain produksi dengan HTTPS di SSO-PBJ.
-   Atur `.env` produksi menggunakan URL SSO dan URL callback produksi.
-   Pastikan akun lokal dan kolom `nip` sudah siap.
-   Jalankan instalasi dependensi di server:

``` bash
composer install --no-dev --optimize-autoloader
```

-   Jalankan migration hanya jika ada migration baru yang sudah ditinjau
    dan diperlukan.
-   Bersihkan atau cache konfigurasi sesuai prosedur deployment.

------------------------------------------------------------------------

## Checklist instalasi

-   [ ] Repository Composer dapat diakses.
-   [ ] Package `pemkab-demak/sso-client` berhasil dipasang.
-   [ ] Route `sso/redirect`, `sso/auto-login`, dan `sso/callback`
    tersedia.
-   [ ] Aplikasi client sudah didaftarkan di SSO-PBJ.
-   [ ] Client ID sudah diatur di `.env`.
-   [ ] `SSO_REDIRECT_URI` sama persis dengan `redirect_uri` di SSO-PBJ.
-   [ ] Tombol SSO menggunakan komponen Blade yang benar.
-   [ ] Resolver dapat mencocokkan NIP/email dan status akun.
-   [ ] Login SSO berhasil.
-   [ ] Login lokal yang sudah ada tetap berfungsi.
-   [ ] Tidak ada token atau secret yang ter-commit ke Git.
