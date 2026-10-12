@extends('layouts.app')

@section('title', 'Manajemen User - SSO Demak')

@section('content')

    <div class="container-fluid py-2">

        {{-- Header Halaman --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

            <div>

                <h4 class="fw-bold mb-1 page-title">
                    <i class="bi bi-people-fill me-2"></i>
                    Manajemen User
                </h4>

                <p class="page-subtitle mb-0 small">
                    Kelola pengguna SSO Demak
                </p>

            </div>

            @can('users.create')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah User
                </a>
            @endcan

        </div>

        {{-- Alert Success --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">

                <i class="bi bi-check-circle-fill me-2"></i>

                <div>{{ session('success') }}</div>

                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>

            </div>
        @endif

        {{-- Alert Error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                <div>{{ session('error') }}</div>

                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>

            </div>
        @endif

        {{-- Card --}}
        <div class="card">

            <div class="card-body p-3 p-md-4">

                {{-- Toolbar: Search + Aksi --}}
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-4 align-items-center">

                    <div class="col-12 col-md-5">

                        <div class="input-group">

                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>

                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari nama, NIP atau email..." value="{{ request('search') }}">

                        </div>

                    </div>

                    <div class="col-12 col-md-auto">

                        <button class="btn btn-primary w-100 w-md-auto">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>

                    </div>

                    @if (request('search'))
                        <div class="col-12 col-md-auto">

                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
                                <i class="bi bi-x-lg me-1"></i>
                                Reset
                            </a>

                        </div>
                    @endif

                    <div class="col-12 col-md-auto ms-md-auto">

                        <a href="{{ route('sso.apps') }}" class="btn btn-outline-primary w-100 w-md-auto">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            Dashboard SSO
                        </a>

                    </div>

                </form>

                {{-- Tabel --}}
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>

                            <tr>
                                <th width="50">#</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th width="220" class="text-center">Aksi</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($users as $user)

                                <tr>

                                    <td class="text-muted">
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">

                                            <div class="user-avatar">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>

                                            <strong>{{ $user->name }}</strong>

                                        </div>
                                    </td>

                                    <td>
                                        <span class="text-muted">
                                            {{ $user->nip ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-muted">
                                            {{ $user->email }}
                                        </span>
                                    </td>

                                    <td>

                                        @forelse($user->roles as $role)
                                            @php
                                                $badge = match ($role->name) {
                                                    'super-admin' => 'danger',
                                                    'admin' => 'primary',
                                                    'user' => 'secondary',
                                                    default => 'dark',
                                                };
                                            @endphp

                                            <span class="badge bg-{{ $badge }} rounded-pill px-3">
                                                {{ $role->name }}
                                            </span>

                                        @empty

                                            <span class="text-muted">-</span>
                                        @endforelse

                                    </td>

                                    <td>

                                        @if ($user->status)
                                            <span class="badge bg-success rounded-pill px-3">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Nonaktif
                                            </span>
                                        @endif

                                    </td>

                                    <td>

                                        <div class="d-flex gap-1 justify-content-center flex-wrap">

                                            @can('users.edit')
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning"
                                                    title="Edit user">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan

                                            @can('users.toggle-status')
                                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf

                                                    <button type="submit" class="btn btn-sm btn-secondary"
                                                        title="{{ $user->status ? 'Nonaktifkan user' : 'Aktifkan user' }}">

                                                        <i
                                                            class="bi {{ $user->status ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>

                                                    </button>

                                                </form>
                                            @endcan

                                            @can('users.reset-password')
                                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf

                                                    <button type="submit" class="btn btn-sm btn-info text-white"
                                                        title="Reset password">
                                                        <i class="bi bi-key"></i>
                                                    </button>

                                                </form>
                                            @endcan

                                            @can('users.delete')
                                                @if ($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus user">
                                                            <i class="bi bi-trash"></i>
                                                        </button>

                                                    </form>
                                                @endif
                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-5">

                                        <div class="empty-state">

                                            <i class="bi bi-inbox"></i>

                                            <p class="mb-0 mt-2 text-muted">
                                                Belum ada user.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- Pagination --}}
                @if ($users->hasPages())
                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <small class="text-muted">
                            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
                            dari {{ $users->total() }} user
                        </small>

                        <div>
                            {{ $users->links() }}
                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>

@endsection

@push('styles')
    <style>
        /* Header halaman */
        .page-title {
            color: var(--brand-primary-dark, #2C5282);
        }

        .page-subtitle {
            color: var(--brand-muted, #718096);
        }

        /* Avatar inisial user */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6B9BD1, #3B6EA5);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(59, 110, 165, 0.25);
        }

        /* Badge */
        .badge {
            font-weight: 500;
            font-size: 0.75rem;
        }

        /* Tombol aksi (icon only) */
        .btn-sm {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        /* Khusus tombol dengan teks (Tambah, Cari, Reset, Dashboard SSO) */
        .btn-primary:not(.btn-sm),
        .btn-outline-primary:not(.btn-sm),
        .btn-outline-secondary:not(.btn-sm) {
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }

        /* Empty state */
        .empty-state i {
            font-size: 3rem;
            color: #CBD5E0;
            opacity: 0.7;
        }

        /* Table style */
        .table thead th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 12px 14px;
        }

        .table tbody td {
            padding: 12px 14px;
            font-size: 0.925rem;
        }

        /* Responsive: tombol full width di mobile */
        @media (max-width: 767.98px) {
            .w-md-auto {
                width: 100%;
            }
        }
    </style>
@endpush
