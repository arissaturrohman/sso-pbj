@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-shield-lock me-2"></i>
                Manajemen Role
            </h4>

            <p class="text-muted mb-0">
                Kelola role dan hak akses pengguna.
            </p>
        </div>

        @can('roles.create')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Role
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

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th width="60" class="text-center">#</th>
                            <th>Role</th>
                            <th>Guard</th>
                            <th width="130" class="text-center">Jumlah User</th>
                            <th width="220" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($roles as $role)

                            <tr>

                                <td class="text-center">
                                    {{ $roles->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    @if ($role->name === 'super-admin')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-shield-fill-check me-1"></i>
                                            {{ $role->name }}
                                        </span>
                                    @elseif($role->name === 'admin')
                                        <span class="badge bg-primary">
                                            <i class="bi bi-person-gear me-1"></i>
                                            {{ $role->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-person me-1"></i>
                                            {{ $role->name }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <code>{{ $role->guard_name }}</code>
                                </td>

                                <td class="text-center">
                                    <span class="badge text-bg-light border">
                                        {{ $role->users_count }}
                                    </span>
                                </td>

                                <td class="text-center">

                                    @can('roles.edit')
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">

                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </a>
                                    @endcan


                                    @can('roles.delete')
                                        @if ($role->name !== 'super-admin')
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                                class="d-inline delete-role-form">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger">

                                                    <i class="bi bi-trash"></i>
                                                    Hapus

                                                </button>

                                            </form>
                                        @endif
                                    @endcan

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">

                                    <i class="bi bi-shield-x fs-2 d-block mb-2"></i>

                                    Belum ada role.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="mt-3">
        {{ $roles->links() }}
    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.delete-role-form')
                .forEach(function(form) {

                    form.addEventListener('submit', function(event) {

                        event.preventDefault();

                        Swal.fire({
                            title: 'Hapus Role?',
                            text: 'Role yang dihapus tidak dapat dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {

                            if (result.isConfirmed) {
                                form.submit();
                            }

                        });

                    });

                });

        });
    </script>
@endpush
