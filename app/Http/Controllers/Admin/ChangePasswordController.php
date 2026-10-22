<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    /**
     * Menampilkan halaman ganti password.
     */
    public function edit(): View
    {
        return view('admin.account.change-password');
    }

    /**
     * Memproses perubahan password pengguna yang sedang login.
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
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.different' => 'Password baru harus berbeda dari password saat ini.',
        ]);

        $user = $request->user();

        // Model User menggunakan cast password => hashed.
        $user->password = $validated['password'];
        $user->save();

        // Perbarui ID sesi tanpa mengeluarkan pengguna dari aplikasi.
        $request->session()->regenerate();

        return redirect()
            ->route('account.password.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }
}