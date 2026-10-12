{{-- Tombol "Login dengan SSO" siap pakai. --}}
<a href="{{ route('sso.redirect') }}" {{ $attributes->merge(['class' => 'btn btn-primary w-100']) }}>
    <i class="bi bi-shield-lock me-2"></i>
    Login dengan SSO Demak
</a>
