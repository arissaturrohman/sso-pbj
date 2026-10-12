@extends('layouts.app')

@section('title', 'Aplikasi SSO')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-grid me-2"></i>
                Aplikasi SSO
            </h4>

            <p class="text-muted mb-0">
                Kelola aplikasi yang terhubung dengan SSO Demak.
            </p>
        </div>

        @can('applications.create')
            <a href="{{ route('admin.applications.create') }}" class="btn btn-primary">

                <i class="bi bi-plus-lg me-1"></i>
                Daftar Aplikasi

            </a>
        @endcan

    </div>


    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>

        </div>
    @endif


    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle me-2"></i>

            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>

        </div>
    @endif


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form method="GET" action="{{ route('admin.applications.index') }}" class="row g-2 mb-4">

                <div class="col-md-6">

                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari nama atau slug aplikasi...">

                </div>

                <div class="col-auto">

                    <button class="btn btn-outline-primary">

                        <i class="bi bi-search me-1"></i>
                        Cari

                    </button>

                </div>

                @if (request('search'))
                    <div class="col-auto">

                        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">

                            Reset

                        </a>

                    </div>
                @endif

            </form>


            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th width="60">#</th>
                            <th>Aplikasi</th>
                            <th>Slug</th>
                            <th>Client ID</th>
                            <th>Status</th>
                            <th width="240" class="text-center">
                                Aksi
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($applications as $application)

                            <tr>

                                <td>
                                    {{ $applications->firstItem() + $loop->index }}
                                </td>

                                <td>

                                    <div class="fw-semibold">
                                        {{ $application->name }}
                                    </div>

                                    @if ($application->description)
                                        <small class="text-muted">
                                            {{ Str::limit($application->description, 70) }}
                                        </small>
                                    @endif

                                </td>

                                <td>
                                    <code>
                                        {{ $application->slug }}
                                    </code>
                                </td>

                                <td>
                                    @if ($application->passport_client_id)
                                        <code>
                                            {{ $application->passport_client_id }}
                                        </code>
                                    @else
                                        <span class="text-danger">
                                            Belum terhubung
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    @if ($application->status)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-pause-circle me-1"></i>
                                            Nonaktif
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center">

                                    @can('applications.edit')
                                        <a href="{{ route('admin.applications.edit', $application) }}"
                                            class="btn btn-sm btn-outline-primary">

                                            <i class="bi bi-pencil-square"></i>

                                        </a>


                                        <form action="{{ route('admin.applications.toggle-status', $application) }}"
                                            method="POST" class="d-inline">

                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-outline-warning">

                                                @if ($application->status)
                                                    <i class="bi bi-pause"></i>
                                                @else
                                                    <i class="bi bi-play"></i>
                                                @endif

                                            </button>

                                        </form>
                                    @endcan


                                    @can('applications.delete')
                                        <form action="{{ route('admin.applications.destroy', $application) }}" method="POST"
                                            class="d-inline delete-application-form">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>
                                    @endcan

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center py-5 text-muted">

                                    <i class="bi bi-grid-3x3-gap fs-1 d-block mb-2"></i>

                                    Belum ada aplikasi terdaftar.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="mt-3">

        {{ $applications->links() }}

    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.delete-application-form')
                .forEach(function(form) {

                    form.addEventListener('submit', function(event) {

                        event.preventDefault();

                        Swal.fire({
                            title: 'Hapus aplikasi?',
                            text: 'Aplikasi tidak dapat menggunakan SSO lagi setelah dihapus.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(function(result) {

                            if (result.isConfirmed) {
                                form.submit();
                            }

                        });

                    });

                });

        });
    </script>
@endpush
