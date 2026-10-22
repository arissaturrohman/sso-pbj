@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
    <div class="container-fluid py-3">

        <div class="mb-4">
            <h4 class="fw-semibold mb-1">Ganti Password</h4>
            <p class="text-muted mb-0">
                Perbarui password akun Anda untuk menjaga keamanan akses SSO.
            </p>
        </div>

        <div class="row">
            <div class="col-12 col-lg-7 col-xl-6">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <div class="fw-semibold mb-1">
                                    Password belum berhasil diperbarui.
                                </div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('account.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    Password Saat Ini
                                </label>

                                <div class="input-group">
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        autocomplete="current-password" required>

                                    <button type="button" class="btn btn-outline-secondary toggle-password"
                                        data-target="current_password" aria-label="Lihat password saat ini"
                                        title="Lihat password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password Baru
                                </label>

                                <div class="input-group">
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password" minlength="8" required>

                                    <button type="button" class="btn btn-outline-secondary toggle-password"
                                        data-target="password" aria-label="Lihat password baru" title="Lihat password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    Gunakan minimal 8 karakter.
                                </small>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">
                                    Konfirmasi Password Baru
                                </label>

                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" autocomplete="new-password" minlength="8" required>

                                    <button type="button" class="btn btn-outline-secondary toggle-password"
                                        data-target="password_confirmation" aria-label="Lihat konfirmasi password"
                                        title="Lihat password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Simpan Password
                                </button>

                                <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Kembali
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

                <div class="text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Jangan membagikan password Anda kepada orang lain.
                </div>

            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.toggle-password').forEach(function(button) {
            button.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                const icon = this.querySelector('i');

                if (!input || !icon) return;

                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';

                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);

                this.setAttribute(
                    'aria-label',
                    isPassword ? 'Sembunyikan password' : 'Lihat password'
                );

                this.setAttribute(
                    'title',
                    isPassword ? 'Sembunyikan password' : 'Lihat password'
                );
            });
        });
    </script>
@endsection
