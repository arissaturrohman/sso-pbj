@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-shield-lock me-2"></i>
                Edit Role
            </h4>

            <p class="text-muted mb-0">
                Kelola nama dan permission role.
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


    <form action="{{ route('admin.roles.update', $role) }}" method="POST">

        @csrf
        @method('PUT')


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nama Role
                    </label>

                    @if ($role->name === 'super-admin')
                        <input type="text" value="{{ $role->name }}" class="form-control" disabled>

                        <div class="form-text text-danger">
                            Role super-admin merupakan role sistem
                            dan tidak dapat diganti namanya.
                        </div>
                    @else
                        <input type="text" name="name" value="{{ old('name', $role->name) }}"
                            class="form-control @error('name') is-invalid @enderror" required>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    @endif

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
                            Atur hak akses role ini.
                        </small>

                    </div>


                    @if ($role->name !== 'super-admin')
                        <div>

                            <button type="button" class="btn btn-sm btn-outline-primary" id="checkAll">

                                Pilih Semua

                            </button>

                            <button type="button" class="btn btn-sm btn-outline-secondary" id="uncheckAll">

                                Hapus Semua

                            </button>

                        </div>
                    @endif

                </div>

            </div>


            <div class="card-body">

                @if ($role->name === 'super-admin')
                    <div class="alert alert-warning">

                        <i class="bi bi-shield-fill-check me-2"></i>

                        <strong>Super Administrator</strong>

                        memiliki seluruh permission sistem.

                        Permission super-admin dikelola otomatis
                        dan tidak dapat dikurangi melalui halaman ini.

                    </div>
                @endif


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
                                            id="permission_{{ md5($permission->name) }}" @checked($role->name === 'super-admin' || in_array($permission->name, old('permissions', $rolePermissions)))
                                            @disabled($role->name === 'super-admin')>


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


                @if ($role->name !== 'super-admin')
                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan

                    </button>
                @else
                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-arrow-repeat me-1"></i>
                        Sinkronkan Permission

                    </button>
                @endif

            </div>

        </div>

    </form>

@endsection


@if ($role->name !== 'super-admin')
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
@endif
