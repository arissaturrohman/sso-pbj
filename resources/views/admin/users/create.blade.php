@extends('layouts.app')

@section('title', 'Tambah User - SSO Demak')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        Tambah User
                    </h5>
                </div>

                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <div class="fw-semibold mb-1">
                                Data belum berhasil disimpan.
                            </div>

                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Password akan dibuat otomatis setelah Anda mengeklik
                        <strong>Simpan User</strong> dan dikirimkan ke nomor
                        WhatsApp yang didaftarkan.
                    </div>

                    <form
                        action="{{ route('admin.users.store') }}"
                        method="POST"
                    >

                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                maxlength="255"
                                autocomplete="name"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nip" class="form-label">
                                NIP
                            </label>

                            <input
                                type="text"
                                name="nip"
                                id="nip"
                                class="form-control @error('nip') is-invalid @enderror"
                                value="{{ old('nip') }}"
                                maxlength="30"
                                required
                            >

                            @error('nip')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                maxlength="255"
                                autocomplete="email"
                                required
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_hp" class="form-label">
                                Nomor HP / WhatsApp
                            </label>

                            <input
                                type="tel"
                                name="no_hp"
                                id="no_hp"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp') }}"
                                placeholder="Contoh: 081234567890"
                                maxlength="20"
                                autocomplete="tel"
                                required
                            >

                            @error('no_hp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="text-muted">
                                Gunakan nomor WhatsApp aktif agar password
                                awal dapat dikirimkan.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">
                                Role
                            </label>

                            <select
                                name="role"
                                id="role"
                                class="form-select @error('role') is-invalid @enderror"
                                required
                            >

                                <option value="">
                                    -- Pilih Role --
                                </option>

                                @foreach($roles as $role)
                                    <option
                                        value="{{ $role->name }}"
                                        @selected(old('role') === $role->name)
                                    >
                                        {{ $role->name }}
                                    </option>
                                @endforeach

                            </select>

                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">

                            <label class="form-label d-block">
                                Status
                            </label>

                            <div class="form-check form-check-inline">

                                <input
                                    type="radio"
                                    name="status"
                                    value="1"
                                    class="form-check-input"
                                    id="statusAktif"
                                    @checked(old('status', '1') == '1')
                                    required
                                >

                                <label
                                    class="form-check-label"
                                    for="statusAktif"
                                >
                                    Aktif
                                </label>

                            </div>

                            <div class="form-check form-check-inline">

                                <input
                                    type="radio"
                                    name="status"
                                    value="0"
                                    class="form-check-input"
                                    id="statusNonaktif"
                                    @checked(old('status') === '0')
                                >

                                <label
                                    class="form-check-label"
                                    for="statusNonaktif"
                                >
                                    Nonaktif
                                </label>

                            </div>

                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-left me-1"></i>
                                Kembali
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="btnSimpanUser"
                            >
                                <i class="bi bi-person-plus me-1"></i>
                                Simpan User
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection