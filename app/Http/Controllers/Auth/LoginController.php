<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = trim($request->login);

        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'nip';

        $credentials = [
            $field => $login,
            'password' => $request->password,
            'status' => true,
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {

            throw ValidationException::withMessages([
                'login' => 'Email/NIP atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Arahkan ke Dashboard SSO
        |--------------------------------------------------------------------------
        |
        | Setelah login, user diarahkan ke halaman pusat SSO yang
        | menyuguhkan seluruh aplikasi client terdaftar.
        |
        */

        return redirect()->intended(route('sso.apps'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}