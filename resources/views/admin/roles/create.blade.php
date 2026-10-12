@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-shield-plus me-2"></i>
                Tambah Role
            </h4>

            <p class="text-muted mb-0">
                Buat role baru dan tentukan permission-nya.
            </p>
        </div>

        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">

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


    <form action="{{ route('admin.roles.store') }}" method="POST">

        @csrf


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nama Role
                    </label>

                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: operator" required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        Gunakan nama yang mudah dipahami, misalnya
                        <code>operator</code>, <code>verifikator</code>,
                        atau <code>pimpinan</code>.
                    </div>

                </div>

            </div>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h5 class="mb-1 fw-bold">
                            Permission
                        </h5>

                        <small class="text-muted">
                            Tentukan hak akses yang dimiliki role ini.
                        </small>
                    </div>

                    <div>

                        <button type="button" class="btn btn-sm btn-outline-primary" id="checkAll">

                            Pilih Semua

                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary" id="uncheckAll">

                            Hapus Semua

                        </button>

                    </div>

                </div>

            </div>


            <div class="card-body">

                @php
                    $permissionGroups = $permissions->groupBy(function ($permission) {
                        return explode('.', $permission->name)[0];
                    });
                @endphp


                <div class="row">

                    @foreach ($permissionGroups as $group => $groupPermissions)
                        <div class="col-lg-4 col-md-6 mb-4">

                            <div class="border rounded p-3 h-100">

                                <h6 class="fw-bold text-uppercase mb-3">

                                    @if ($group === 'users')
                                        <i class="bi bi-people me-1"></i>
                                    @elseif($group === 'roles')
                                        <i class="bi bi-shield-lock me-1"></i>
                                    @elseif($group === 'applications')
                                        <i class="bi bi-grid me-1"></i>
                                    @else
                                        <i class="bi bi-key me-1"></i>
                                    @endif

                                    {{ $group }}

                                </h6>


                                @foreach ($groupPermissions as $permission)
                                    <div class="form-check mb-2">

                                        <input class="form-check-input permission-checkbox" type="checkbox"
                                            name="permissions[]" value="{{ $permission->name }}"
                                            id="permission_{{ md5($permission->name) }}" @checked(in_array($permission->name, old('permissions', [])))>

                                        <label class="form-check-label" for="permission_{{ md5($permission->name) }}">

                                            {{ $permission->name }}

                                        </label>

                                    </div>
                                @endforeach

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>


            <div class="card-footer bg-white text-end">

                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">

                    Batal

                </a>

                <button type="submit" class="btn btn-primary">

                    <i class="bi bi-save me-1"></i>
                    Simpan Role

                </button>

            </div>

        </div>

    </form>

@endsection


@push('scripts')
    <script>
        document.getElementById('checkAll').addEventListener('click', function() {

            document.querySelectorAll('.permission-checkbox')
                .forEach(function(checkbox) {
                    checkbox.checked = true;
                });

        });


        document.getElementById('uncheckAll').addEventListener('click', function() {

            document.querySelectorAll('.permission-checkbox')
                .forEach(function(checkbox) {
                    checkbox.checked = false;
                });

        });
    </script>
@endpush
