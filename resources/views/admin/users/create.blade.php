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

                            <ul class="mb-0">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                    <form
                        action="{{ route('admin.users.store') }}"
                        method="POST"
                    >

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                NIP
                            </label>

                            <input
                                type="text"
                                name="nip"
                                class="form-control"
                                value="{{ old('nip') }}"
                                maxlength="30"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                required
                            >

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Password
                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        required
                                    >

                                    <small class="text-muted">
                                        Minimal 8 karakter.
                                    </small>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Konfirmasi Password
                                    </label>

                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                        required
                                    >

                                </div>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Role
                            </label>

                            <select
                                name="role"
                                class="form-select"
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

                        <div class="d-flex gap-2">

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="btn btn-secondary"
                            >
                                Kembali
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
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