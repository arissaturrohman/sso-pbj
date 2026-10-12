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

                        <label class="form-label fw-semibold">
                            Nama Aplikasi
                        </label>

                        <input type="text" name="name" value="{{ old('name', $application->name) }}"
                            class="form-control" required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Slug
                        </label>

                        <input type="text" name="slug" value="{{ old('slug', $application->slug) }}"
                            class="form-control" required>

                    </div>


                    <div class="col-12 mb-3">

                        <label class="form-label fw-semibold">
                            Deskripsi
                        </label>

                        <textarea name="description" rows="3" class="form-control">{{ old('description', $application->description) }}</textarea>

                    </div>


                    <div class="col-12 mb-4">

                        <label class="form-label fw-semibold">
                            Redirect URI
                        </label>

                        <input type="url" name="redirect_uri"
                            value="{{ old('redirect_uri', $application->redirect_uri) }}" class="form-control" required>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" class="form-select">

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
                    OAuth Client
                </h6>


                <div class="mb-3">

                    <label class="form-label">
                        Client ID
                    </label>

                    <input type="text" class="form-control" value="{{ $application->client_id }}" readonly>

                </div>


                <div class="alert alert-warning">

                    <i class="bi bi-shield-lock me-2"></i>

                    <strong>Client Secret</strong>

                    tidak ditampilkan di halaman ini untuk menjaga
                    keamanan credential aplikasi.

                </div>


                @can('applications.edit')
                    <div class="border rounded p-3">

                        <h6 class="fw-semibold">
                            Regenerate Client Secret
                        </h6>

                        <p class="text-muted small">
                            Gunakan hanya jika client secret lama
                            sudah tidak aman atau hilang.
                        </p>

                        <button type="button" class="btn btn-outline-danger" id="regenerateSecretButton">

                            <i class="bi bi-key me-1"></i>
                            Generate Secret Baru

                        </button>

                    </div>
                @endcan

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

@push('scripts')
    <script>
        document.getElementById('regenerateSecretButton')
            .addEventListener('click', function() {

                Swal.fire({
                    title: 'Generate Client Secret baru?',
                    text: 'Secret lama tidak akan dapat digunakan lagi.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Generate',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(result) {

                    if (!result.isConfirmed) {
                        return;
                    }

                    const form = document.createElement('form');

                    form.method = 'POST';
                    form.action =
                        "{{ route('admin.applications.regenerate-secret', $application) }}";

                    const csrf = document.createElement('input');

                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = "{{ csrf_token() }}";

                    form.appendChild(csrf);

                    document.body.appendChild(form);

                    form.submit();

                });

            });
    </script>
@endpush
