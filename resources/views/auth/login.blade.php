@extends('layouts.app')

@section('title', 'Login - SSO PBJ')

@section('content')

<div class="min-vh-100 d-flex align-items-center login-bg position-relative overflow-hidden">

    {{-- Ornamen dekoratif background --}}
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="container position-relative" style="z-index: 1;">

        <div class="row justify-content-center">

            <div class="col-md-6 col-lg-5 col-xl-4">

                <div class="card border-0 login-card">

                    <div class="card-body p-4 p-md-5">

                        {{-- Logo & Header --}}
                        <div class="text-center mb-4">

                            <div class="logo-wrapper mx-auto mb-3">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>

                            <h3 class="fw-bold mb-1 brand-title">
                                SSO PBJ
                            </h3>

                            <p class="brand-subtitle mb-0 small">
                                Single Sign-On
                            </p>

                        </div>

                        {{-- Alert Error --}}
                        @if ($errors->any())

                            <div class="alert alert-danger d-flex align-items-center border-0 rounded-3">

                                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                                <div>
                                    {{ $errors->first() }}
                                </div>

                            </div>

                        @endif

                        <form
                            method="POST"
                            action="{{ route('login.process') }}"
                        >

                            @csrf

                            {{-- Input Login --}}
                            <div class="mb-3">

                                <label class="form-label fw-semibold small form-label-custom">
                                    Email / NIP
                                </label>

                                <div class="input-group input-group-lg">

                                    <span class="input-group-text">
                                        <i class="bi bi-person-fill"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="login"
                                        value="{{ old('login') }}"
                                        class="form-control @error('login') is-invalid @enderror"
                                        placeholder="Masukkan email atau NIP"
                                        autofocus
                                        required
                                    >

                                </div>

                                @error('login')
                                    <div class="error-text small mt-1">
                                        <i class="bi bi-info-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror

                            </div>

                            {{-- Input Password --}}
                            <div class="mb-3">

                                <label class="form-label fw-semibold small form-label-custom">
                                    Password
                                </label>

                                <div class="input-group input-group-lg">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="form-control"
                                        placeholder="Masukkan password"
                                        required
                                    >

                                    <span class="input-group-text toggle-password" role="button">
                                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                    </span>

                                </div>

                            </div>

                            {{-- Remember Me --}}
                            <div class="form-check mb-4">

                                <input
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    class="form-check-input"
                                    id="remember"
                                >

                                <label
                                    class="form-check-label small form-label-custom"
                                    for="remember"
                                >
                                    Ingat saya
                                </label>

                            </div>

                            {{-- Tombol Login --}}
                            <button
                                type="submit"
                                class="btn btn-login btn-lg w-100 fw-semibold"
                            >
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Login
                            </button>

                        </form>

                    </div>

                </div>

                {{-- Footer --}}
                <div class="text-center mt-4">

                    <small class="footer-text">
                        <i class="bi bi-building me-1"></i>
                        SSO Pemerintah Kabupaten Demak
                    </small>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@push('styles')
<style>
    /* =========================================
       TEMA: Soft Blue Elegant (Monokromatik)
       -----------------------------------------
       Base   : #3B6EA5  (biru utama)
       Dark   : #2C5282  (biru tua)
       Light  : #E8F0F9  (biru sangat muda)
       Soft   : #F5F8FC  (biru pucat)
       Accent : #6B9BD1  (biru sedang)
       Text   : #2D3748  (abu gelap)
       Muted  : #718096  (abu sedang)
    ========================================= */

    /* Background - gradient biru lembut satu tone */
    .login-bg {
        background: linear-gradient(160deg, #E8F0F9 0%, #F5F8FC 50%, #E8F0F9 100%);
    }

    /* Ornamen lingkaran - satu tone biru */
    .bg-circle {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
    }

    .bg-circle-1 {
        width: 500px;
        height: 500px;
        top: -200px;
        left: -200px;
        background: rgba(107, 155, 209, 0.18);
    }

    .bg-circle-2 {
        width: 400px;
        height: 400px;
        bottom: -150px;
        right: -150px;
        background: rgba(59, 110, 165, 0.12);
    }

    /* Card putih bersih */
    .login-card {
        border-radius: 20px;
        background: #FFFFFF;
        box-shadow: 0 20px 60px rgba(59, 110, 165, 0.12);
        animation: fadeInUp 0.6s ease-out;
    }

    /* Logo wrapper - biru solid */
    .logo-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        background: #3B6EA5;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 24px rgba(59, 110, 165, 0.3);
    }

    .logo-wrapper i {
        font-size: 1.9rem;
        color: #FFFFFF;
    }

    /* Brand typography */
    .brand-title {
        color: #2C5282;
        letter-spacing: -0.5px;
    }

    .brand-subtitle {
        color: #718096;
    }

    .form-label-custom {
        color: #4A5568;
    }

    .footer-text {
        color: #718096;
    }

    .error-text {
        color: #C53030;
    }

    /* ===== INPUT GROUP ===== */
    .input-group-text {
        background-color: #F5F8FC;
        border: 1.5px solid #E1E8F0;
        color: #3B6EA5;
        border-radius: 12px 0 0 12px !important;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .form-control {
        border: 1.5px solid #E1E8F0;
        border-left: none;
        background-color: #F5F8FC;
        color: #2D3748;
        padding: 0.75rem 1rem;
        border-radius: 0 !important;
        font-size: 0.95rem;
    }

    .form-control::placeholder {
        color: #A0AEC0;
    }

    .form-control:focus {
        background-color: #FFFFFF;
        border-color: #6B9BD1;
        box-shadow: none;
    }

    .input-group:focus-within .input-group-text {
        border-color: #6B9BD1;
        background-color: #FFFFFF;
    }

    .input-group .form-control:last-child,
    .input-group .toggle-password {
        border-radius: 0 12px 12px 0 !important;
    }

    .toggle-password {
        background-color: #F5F8FC;
        border: 1.5px solid #E1E8F0;
        border-left: none;
        color: #718096;
        cursor: pointer;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .toggle-password:hover {
        color: #3B6EA5;
    }

    /* ===== BUTTON ===== */
    .btn-login {
        background: #3B6EA5;
        border: none;
        color: #FFFFFF;
        border-radius: 12px;
        padding: 0.85rem;
        font-size: 1rem;
        letter-spacing: 0.3px;
        transition: all 0.25s ease;
        box-shadow: 0 6px 16px rgba(59, 110, 165, 0.25);
    }

    .btn-login:hover,
    .btn-login:focus {
        background: #2C5282;
        color: #FFFFFF;
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(59, 110, 165, 0.35);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    /* ===== CHECKBOX ===== */
    .form-check-input {
        border: 1.5px solid #CBD5E0;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #3B6EA5;
        border-color: #3B6EA5;
    }

    .form-check-input:focus {
        border-color: #6B9BD1;
        box-shadow: 0 0 0 3px rgba(107, 155, 209, 0.15);
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
    }

    /* ===== ALERT ===== */
    .alert-danger {
        background-color: #FFF5F5;
        color: #C53030;
        font-size: 0.875rem;
        border: 1px solid #FED7D7;
    }

    /* ===== ANIMASI ===== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(24px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');

        if (toggle && passwordInput && icon) {
            toggle.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('bi-eye-slash');
                icon.classList.toggle('bi-eye');
            });
        }
    });
</script>
@endpush