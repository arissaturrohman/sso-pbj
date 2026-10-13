@extends('layouts.app')

@section('title', 'Edit Aplikasi')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-pencil-square me-2"></i>
                Edit Aplikasi
            </h4>

            <p class="text-muted mb-0">
                Kelola konfigurasi aplikasi SSO.
            </p>
        </div>

        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.applications.update', $application) }}">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Nama Aplikasi
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $application->name) }}"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="slug" class="form-label fw-semibold">
                            Slug
                        </label>

                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            value="{{ old('slug', $application->slug) }}"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Deskripsi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="form-control"
                        >{{ old('description', $application->description) }}</textarea>
                    </div>

                    <div class="col-12 mb-4">
                        <label for="redirect_uri" class="form-label fw-semibold">
                            Redirect URI
                        </label>

                        <input
                            type="url"
                            id="redirect_uri"
                            name="redirect_uri"
                            value="{{ old('redirect_uri', $application->redirect_uri) }}"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label fw-semibold">
                            Status
                        </label>

                        <select id="status" name="status" class="form-select" required>
                            <option value="1" @selected(old('status', $application->status) == 1)>
                                Aktif
                            </option>

                            <option value="0" @selected(old('status', $application->status) == 0)>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                </div>

                <hr class="my-4">

                <h6 class="fw-bold mb-3">
                    <i class="bi bi-shield-lock me-2"></i>
                    OAuth Client
                </h6>

                <div class="mb-3">
                    <label for="client_id" class="form-label">
                        Client ID
                    </label>

                    <input
                        type="text"
                        id="client_id"
                        class="form-control"
                        value="{{ $application->passport_client_id ?? '' }}"
                        readonly
                    >
                </div>

                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Client Secret tidak ditampilkan untuk menjaga keamanan
                    kredensial aplikasi. Pengaturan credential dikelola melalui
                    mekanisme OAuth Client.
                </div>
            </div>

            <div class="card-footer bg-white text-end">
                <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

@endsection