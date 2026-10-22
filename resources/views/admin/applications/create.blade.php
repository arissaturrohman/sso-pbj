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

                    <div class="mb-3">
                        <label for="access_type" class="form-label">
                            Jenis Akses <span class="text-danger">*</span>
                        </label>

                        <select name="access_type" id="access_type"
                            class="form-select @error('access_type') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Akses --</option>

                            <option value="all_users" {{ old('access_type') === 'all_users' ? 'selected' : '' }}>
                                Semua Pengguna SSO
                            </option>

                            <option value="pbj_only" {{ old('access_type') === 'pbj_only' ? 'selected' : '' }}>
                                Khusus Pegawai PBJ
                            </option>
                        </select>

                        @error('access_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Tentukan siapa yang boleh mengakses aplikasi ini melalui SSO.
                        </small>
                    </div>

                    <div class="mb-3" id="user-assignment-section" style="display: none;">
                        <label for="assigned_users" class="form-label">
                            Pengguna yang Diberi Akses
                        </label>

                        <input type="text" id="userSearch" class="form-control mb-2" placeholder="Cari pengguna berdasarkan nama atau email...">

                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                            @foreach($users as $user)
                                <div class="form-check">
                                    <input class="form-check-input user-checkbox" type="checkbox" name="assigned_users[]"
                                           value="{{ $user->id }}" id="user_{{ $user->id }}"
                                           {{ in_array($user->id, old('assigned_users', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                        {{ $user->name }} <span class="text-muted small">({{ $user->email }})</span>
                                    </label>
                                </div>
                            @endforeach
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accessTypeSelect = document.getElementById('access_type');
            const userAssignmentSection = document.getElementById('user-assignment-section');
            const userSearch = document.getElementById('userSearch');

            function toggleUserAssignment() {
                if (accessTypeSelect.value === 'pbj_only') {
                    userAssignmentSection.style.display = 'block';
                } else {
                    userAssignmentSection.style.display = 'none';
                }
            }

            // Search/Filter functionality
            userSearch.addEventListener('input', function(e) {
                const filter = e.target.value.toLowerCase();
                document.querySelectorAll('.form-check').forEach(function(item) {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(filter) ? '' : 'none';
                });
            });

            // Initial check
            toggleUserAssignment();

            // Listen for changes
            accessTypeSelect.addEventListener('change', toggleUserAssignment);
        });
    </script>
    @endpush

@endsection
