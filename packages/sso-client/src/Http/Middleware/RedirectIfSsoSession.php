<?php

namespace Demak\SsoClient\Http\Middleware;

use Closure;
use Demak\SsoClient\Support\SsoConfig;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Untuk pola "ketik URL langsung → auto-redirect diam-diam ke SSO".
 *
 * Pasang di route login (guest): kalau user belum login lokal DAN
 * aplikasi dikonfigurasi SSO, lempar ke sso.redirect. Karena route
 * redirect memakai session yang sama, callback akan kembali normal.
 *
 * Guard loop-nya:
 * - ?sso=off → jangan redirect (dipakai tombol Batal / fallback manual).
 * - ?sso=1 → paksa redirect (dipakai overlay JS hitung-mundur).
 * - error di session → jangan redirect (biarkan pesan error terbaca).
 */
class RedirectIfSsoSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $config = new SsoConfig;

        if (auth()->check() || !$config->ready()) {
            return $next($request);
        }

        if ($request->boolean('sso') === false && $request->has('sso') && $request->query('sso') === 'off') {
            return $next($request);
        }

        // Jangan ganggu saat user baru saja gagal login (manual maupun SSO).
        if ($request->session()->has('error') || $request->session()->has('errors')) {
            return $next($request);
        }

        // Hanya GET ke halaman login, dan hindari loop dari ?sso=1 itu sendiri.
        if (!$request->isMethod('get') || $request->boolean('sso')) {
            return $next($request);
        }

        return redirect()->route('sso.redirect');
    }
}
