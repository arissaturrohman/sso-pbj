@extends('layouts.app')

@section('title', 'Aplikasi Saya — SSO Demak')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>
                Aplikasi Saya
            </h4>
            <p class="text-muted mb-0 small">
                Login satu kali di SSO, lalu klik "Buka Aplikasi" — tab baru langsung masuk tanpa password lagi.
                Biarkan tab SSO ini tetap terbuka untuk pindah aplikasi.
            </p>
        </div>

        @can('applications.view')
        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-gear me-1"></i>
            Kelola Aplikasi
        </a>
        @endcan
    </div>

    @if ($applications->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted mb-3">Belum ada aplikasi client yang terdaftar.</p>
                <a href="{{ route('admin.applications.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Aplikasi
                </a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($applications as $app)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm {{ $app->status ? '' : 'border-warning' }}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <h6 class="card-title mb-0 fw-semibold text-truncate" title="{{ $app->name }}">
                                    {{ $app->name }}
                                </h6>

                                @if ($app->status)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle flex-shrink-0">
                                        <i class="bi bi-circle-fill me-1" style="font-size: .45rem; vertical-align: middle;"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle flex-shrink-0">
                                        Non-aktif
                                    </span>
                                @endif
                            </div>

                            <p class="text-muted small mb-2">
                                <code class="small text-break">{{ $app->slug }}</code>
                            </p>

                            @if (!empty($app->description))
                                <p class="text-muted small mb-3 flex-grow-1">
                                    {{ $app->description }}
                                </p>
                            @else
                                <p class="text-muted small mb-3 flex-grow-1 fst-italic">Tanpa deskripsi.</p>
                            @endif

                            <p class="text-muted small mb-3 text-break">
                                <i class="bi bi-link-45deg me-1"></i>{{ $app->redirect_uri }}
                            </p>

                            @if ($app->status && $app->login_url)
                                <a href="{{ $app->login_url }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-primary w-100">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    Buka Aplikasi
                                </a>
                            @else
                                <button type="button" class="btn btn-outline-secondary w-100" disabled>
                                    <i class="bi bi-pause-circle me-1"></i>
                                    Tidak tersedia
                                </button>
                                @if (!$app->status)
                                    <small class="text-warning-emphasis d-block text-center mt-2">
                                        Aplikasi ini sedang dinonaktifkan.
                                    </small>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="alert alert-info small mt-4 mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Setelah login, biarkan tab SSO ini tetap terbuka — setiap klik "Buka Aplikasi"
            membuka tab baru yang langsung dalam keadaan <strong>sudah login</strong>.
        </div>
    @endif
@endsection
