<?php

namespace App\Http\Middleware;

use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSsoApplicationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
         * Middleware ini hanya memeriksa endpoint otorisasi OAuth.
         * Request ke halaman lain tetap berjalan seperti biasa.
         */
        if (! $request->is('oauth/authorize')) {
            return $next($request);
        }

        /*
         * Jika pengguna belum login, biarkan middleware autentikasi
         * Laravel Passport menangani proses login.
         */
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        /*
         * Ambil client_id dari query string atau form request.
         */
        $clientId = $request->input(
            'client_id',
            $request->query('client_id')
        );

        /*
         * Jika client_id tidak tersedia, biarkan Passport
         * melakukan validasi OAuth seperti biasanya.
         */
        if (! $clientId) {
            return $next($request);
        }

        /*
         * Cari aplikasi berdasarkan Passport client ID.
         * Bukan berdasarkan applications.id.
         */
        $application = Application::where(
            'passport_client_id',
            $clientId
        )->first();

        /*
         * Aplikasi OAuth harus terdaftar di tabel applications.
         */
        if (! $application) {
            abort(403, 'Aplikasi belum terdaftar di SSO.');
        }

        /*
         * Tolak aplikasi yang tidak aktif.
         * Sesuaikan nilai status jika proyek menggunakan
         * konvensi status yang berbeda.
         */
        if (
            isset($application->status) &&
            ! in_array(
                $application->status,
                ['active', 'aktif', '1', 1, true],
                true
            )
        ) {
            abort(403, 'Aplikasi sedang tidak aktif.');
        }

        /*
         * Tolak pengguna yang status akunnya bukan aktif,
         * jika model users memang memiliki kolom status.
         */
        if (
            array_key_exists('status', $user->getAttributes()) &&
            ! in_array(
                $user->status,
                ['active', 'aktif', '1', 1, true],
                true
            )
        ) {
            abort(403, 'Akun Anda tidak aktif.');
        }

        /*
         * Aplikasi khusus PBJ memerlukan pemberian akses
         * secara eksplisit melalui tabel application_user.
         */
        if ($application->access_type === 'pbj_only') {
            $hasAccess = $application->users()
                ->where('users.id', $user->getKey())
                ->exists();

            if (! $hasAccess) {
                abort(
                    403,
                    'Anda belum diberikan akses ke aplikasi ini.'
                );
            }
        }

        /*
         * Aplikasi dengan access_type all_users dapat diakses
         * oleh pengguna SSO aktif sesuai kebijakan aplikasi.
         */
        return $next($request);
    }
}