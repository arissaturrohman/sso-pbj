{{--
    Overlay auto-redirect ke SSO (menggantikan snippet inline di tiap aplikasi).

    Pakai di halaman login: <x-sso-client::auto-redirect />
    Query ?sso=off menonaktifkannya (tombol Batal / habis gagal login).
--}}
@if (!request()->has('sso') || request()->query('sso') !== 'off')
<div id="ssoAutoOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
    style="z-index: 9999; background: rgba(255,255,255,0.98);"
    role="alertdialog" aria-label="Mengalihkan ke SSO">
    <div class="text-center p-4">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <h5 class="fw-semibold mb-2">Mengalihkan ke SSO Demak...</h5>
        <p class="text-muted mb-4 small">Login sekali, akses semua aplikasi.</p>
        <button type="button" id="ssoCancelBtn" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x-lg me-1"></i> Batal — login manual
        </button>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('ssoAutoOverlay');
    var cancelBtn = document.getElementById('ssoCancelBtn');
    if (!overlay || !cancelBtn) return;

    var cancelled = false;
    var timer = setTimeout(function () {
        if (!cancelled) window.location.href = "{{ route('sso.redirect') }}";
    }, 1500);

    function cancel(focusLogin) {
        if (cancelled) return;
        cancelled = true;
        clearTimeout(timer);
        overlay.style.display = 'none';
        // Tandai URL agar middleware tidak me-redirect ulang.
        if (window.history && window.history.replaceState) {
            var url = new URL(window.location.href);
            url.searchParams.set('sso', 'off');
            window.history.replaceState({}, '', url.toString());
        }
        if (focusLogin !== false) {
            var login = document.getElementById('login');
            if (login) login.focus();
        }
    }

    cancelBtn.addEventListener('click', function () { cancel(true); });

    ['login', 'password'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', function () { cancel(false); }, { once: true });
    });
})();
</script>
@endif
