<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    /**
     * Menampilkan form ganti password.
     */
    public function edit(): View
    {
        return view('admin.account.change-password');
    }

    /**
     * Memperbarui password pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:current_password',
            ],
        ], [
            'current_password.required' =>
            'Password saat ini wajib diisi.',

            'current_password.current_password' =>
            'Password saat ini tidak sesuai.',

            'password.required' =>
            'Password baru wajib diisi.',

            'password.min' =>
            'Password baru minimal 8 karakter.',

            'password.confirmed' =>
            'Konfirmasi password baru tidak cocok.',

            'password.different' =>
            'Password baru harus berbeda dari password saat ini.',
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Simpan password baru
        |--------------------------------------------------------------------------
        */
        $user->password = $validated['password'];

        /*
        |--------------------------------------------------------------------------
        | Tandai bahwa pengguna sudah mengganti password
        |--------------------------------------------------------------------------
        */
        $user->must_change_password = false;

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | Regenerasi session
        |--------------------------------------------------------------------------
        */
        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Kembali ke URL tujuan jika tersedia
        |--------------------------------------------------------------------------
        */
        $intendedUrl = $request->session()->pull('url.intended');

        if (
            is_string($intendedUrl) &&
            str_starts_with($intendedUrl, url('/'))
        ) {
            return redirect()->to($intendedUrl)
                ->with(
                    'success',
                    'Password berhasil diperbarui.'
                );
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Password berhasil diperbarui.'
            );
    }
}