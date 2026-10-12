<?php

namespace Demak\SsoClient\Http\Controllers;

use Demak\SsoClient\Contracts\SsoUserResolver;
use Demak\SsoClient\Events\SsoLoginSucceeded;
use Demak\SsoClient\Support\SsoConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Alur OAuth2 Authorization Code + PKCE, identik dengan yang sudah
 * teruji di rup-app (redirect → session state/verifier → callback
 * tukar code → ambil /api/user → cocokkan user lokal → Auth::login).
 */
class SsoController extends Controller
{
    /**
     * Bangun state + PKCE lalu lempar ke halaman authorize server SSO.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $config = new SsoConfig;

        if (!$config->ready()) {
            return redirect()->route($config->loginRoute())
                ->with('error', 'Konfigurasi SSO belum lengkap.');
        }

        $state = Str::random(64);
        $codeVerifier = Str::random(96);

        $codeChallenge = rtrim(
            strtr(
                base64_encode(hash('sha256', $codeVerifier, true)),
                '+/',
                '-_'
            ),
            '='
        );

        $request->session()->put([
            'sso_state' => $state,
            'sso_code_verifier' => $codeVerifier,
        ]);

        $query = http_build_query([
            'client_id' => $config->clientId(),
            'redirect_uri' => $config->redirectUri(),
            'response_type' => 'code',
            'scope' => $config->scope(),
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect()->away($config->baseUrl() . '/oauth/authorize?' . $query);
    }

    /**
     * Terima code dari SSO, tukar dengan token, ambil profil,
     * cocokkan user lokal, lalu login.
     */
    public function callback(Request $request, SsoUserResolver $resolver): RedirectResponse
    {
        $config = new SsoConfig;

        if ($request->filled('error')) {
            $request->session()->forget(['sso_state', 'sso_code_verifier']);

            return $this->failed($config, 'Login SSO dibatalkan atau tidak berhasil.');
        }

        $expectedState = $request->session()->pull('sso_state');
        $codeVerifier = $request->session()->pull('sso_code_verifier');
        $returnedState = (string) $request->query('state', '');
        $code = $request->query('code');

        if (
            !is_string($expectedState) || $expectedState === ''
            || !hash_equals($expectedState, $returnedState)
            || !is_string($codeVerifier) || $codeVerifier === ''
            || !is_string($code) || $code === ''
        ) {
            return $this->failed($config, 'Validasi SSO gagal. Silakan coba login kembali.');
        }

        if (!$config->ready()) {
            return $this->failed($config, 'Konfigurasi SSO belum lengkap.');
        }

        try {
            $tokenResponse = Http::asForm()
                ->acceptJson()
                ->timeout($config->timeout())
                ->post($config->baseUrl() . '/oauth/token', array_filter([
                    'grant_type' => 'authorization_code',
                    'client_id' => $config->clientId(),
                    'client_secret' => $config->clientSecret(),
                    'redirect_uri' => $config->redirectUri(),
                    'code' => $code,
                    'code_verifier' => $codeVerifier,
                ], fn ($value) => $value !== null && $value !== ''));

            if (!$tokenResponse->successful() || !$tokenResponse->json('access_token')) {
                return $this->failed($config, 'Gagal memverifikasi login ke server SSO.');
            }

            $profileResponse = Http::withToken($tokenResponse->json('access_token'))
                ->acceptJson()
                ->timeout($config->timeout())
                ->get($config->baseUrl() . $config->userApi());

            if (!$profileResponse->successful()) {
                return $this->failed($config, 'Gagal mengambil identitas dari server SSO.');
            }

            $profile = $profileResponse->json('data');

            if (!is_array($profile)) {
                $profile = $profileResponse->json();
            }

            if (!is_array($profile) || (empty($profile['email']) && empty($profile['nip']))) {
                return $this->failed($config, 'Identitas dari SSO tidak lengkap.');
            }

            $localUser = $resolver->resolve($profile);

            if (!$localUser) {
                return $this->failed(
                    $config,
                    'Akun tidak ditemukan atau tidak aktif. Silakan hubungi administrator.'
                );
            }

            Auth::login($localUser);
            $request->session()->regenerate();

            event(new SsoLoginSucceeded($localUser, $profile));

            return redirect()->intended(route($config->homeRoute(), absolute: false));
        } catch (Throwable $e) {
            report($e);

            return $this->failed($config, 'Server SSO tidak dapat dihubungi. Silakan coba kembali.');
        }
    }

    private function failed(SsoConfig $config, string $message): RedirectResponse
    {
        return redirect()->route($config->loginRoute())->with('error', $message);
    }
}
