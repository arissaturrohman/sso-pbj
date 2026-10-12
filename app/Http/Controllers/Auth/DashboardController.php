<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Application;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman pusat SSO (daftar semua aplikasi client).
     *
     * Tombol "Buka Aplikasi" mengarah ke /sso/redirect milik client,
     * sehingga client yang men-generate state + PKCE di session-nya
     * sendiri. Dengan session SSO masih aktif dan skip-consent,
     * user langsung kembali dalam keadaan sudah login.
     */
    public function index()
    {
        $applications = Application::with('passportClient')
            ->orderByDesc('status')
            ->orderBy('name')
            ->get();

        return view('sso.dashboard', compact('applications'));
    }
}
