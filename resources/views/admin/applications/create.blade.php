@extends('layouts.app')

@section('title', 'Daftar Aplikasi')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                <i class="bi bi-grid-plus me-2"></i>
                Daftar Aplikasi
            </h4>

            <p class="text-muted mb-0">
                Daftarkan aplikasi baru ke SSO Demak.
            </p>

        </div>

        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Kembali

        </a>

    </div>


    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Terdapat kesalahan:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <form method="POST" action="{{ route('admin.applications.store') }}">

        @csrf


        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">

                <h5 class="mb-0 fw-bold">
                    Informasi Aplikasi
                </h5>

            </div>


            <div class="card-body">

                <div class="row">

                    <div class="mb-3">
                        <label class="form-label">
                            Nama Aplikasi
                        </label>

                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="Contoh: DELIMA" required>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Slug
                        </label>

                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug') }}" placeholder="delima" required>

                        @error('slug')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi aplikasi">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Redirect URI
                        </label>

                        <input type="url" name="redirect_uri"
                            class="form-control @error('redirect_uri') is-invalid @enderror"
                            value="{{ old('redirect_uri') }}" placeholder="https://delima.demakkab.go.id/oauth/callback"
                            required>

                        @error('redirect_uri')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            URI tujuan callback OAuth setelah proses login SSO selesai.
                        </div>
                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="1" @selected(old('status', '1') == '1')>

                                Aktif

                            </option>

                            <option value="0" @selected(old('status') === '0')>

                                Nonaktif

                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <div class="card-footer bg-white text-end">

                <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">

                    Batal

                </a>

                <button type="submit" class="btn btn-primary">

                    <i class="bi bi-save me-1"></i>
                    Daftarkan Aplikasi

                </button>

            </div>

        </div>

    </form>

@endsection
