@extends('layouts.app')

@section('title', 'Edit User - SSO Demak')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-warning">

                    <h5 class="mb-0">
                        Edit User
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
                        action="{{ route('admin.users.update', $user) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PUT')

                        <div class="mb-3">

                            <label class="form-label">
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
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
                                value="{{ old('nip', $user->nip) }}"
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
                                value="{{ old('email', $user->email) }}"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                No HP / Whatsapp
                            </label>

                            <input
                                type="text"
                                name="no_hp"
                                class="form-control"
                                value="{{ old('no_hp', $user->no_hp) }}"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Role
                            </label>

                            @php
                                $currentRole = $user->roles->first()?->name;
                            @endphp

                            <select
                                name="role"
                                class="form-select"
                                required
                            >

                                @foreach($roles as $role)

                                    <option
                                        value="{{ $role->name }}"
                                        @selected(
                                            old('role', $currentRole)
                                            === $role->name
                                        )
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
                                    @checked(old('status', $user->status) == 1)
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
                                    @checked(old('status', $user->status) == 0)
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
                                Simpan Perubahan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection