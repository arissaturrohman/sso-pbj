<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Pengguna yang belum login tetap mengikuti alur normal.
        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        $path = $request->path();

        // Izinkan halaman dan proses ganti password.
        if ($path === 'account/password') {
            return $next($request);
        }

        // Izinkan pengguna keluar dari akun.
        if ($path === 'logout') {
            return $next($request);
        }

        // Jangan menghalangi pertukaran token OAuth di sisi server.
        if (in_array($path, [
            'oauth/token',
            'oauth/token/refresh',
        ], true)) {
            return $next($request);
        }

        // Simpan URL awal, termasuk query string OAuth dan PKCE.
        if ($request->isMethod('GET')) {
            $request->session()->put(
                'url.intended',
                $request->getRequestUri()
            );
        }

        return redirect()
            ->route('account.password.edit')
            ->with(
                'warning',
                'Anda harus mengganti password awal sebelum melanjutkan.'
            );
    }
}