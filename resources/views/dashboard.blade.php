@extends('layouts.app')

@section('title', 'Dashboard - SSO Demak')

@section('content')

<div class="mb-4">

    <h3 class="fw-bold mb-1">
        Dashboard
    </h3>

    <p class="text-muted mb-0">
        Selamat datang di SSO Demak.
    </p>

</div>


<div class="row g-4">

    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="bg-primary bg-opacity-10
                                text-primary rounded p-3">

                        <i class="bi bi-person fs-3"></i>

                    </div>

                    <div class="ms-3">

                        <small class="text-muted">
                            Nama
                        </small>

                        <h5 class="mb-0">
                            {{ auth()->user()->name }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="bg-success bg-opacity-10
                                text-success rounded p-3">

                        <i class="bi bi-person-vcard fs-3"></i>

                    </div>

                    <div class="ms-3">

                        <small class="text-muted">
                            NIP
                        </small>

                        <h5 class="mb-0">
                            {{ auth()->user()->nip }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="bg-warning bg-opacity-10
                                text-warning rounded p-3">

                        <i class="bi bi-shield-check fs-3"></i>

                    </div>

                    <div class="ms-3">

                        <small class="text-muted">
                            Role
                        </small>

                        <h5 class="mb-0">

                            @forelse(auth()->user()->roles as $role)

                                <span class="badge bg-primary">
                                    {{ $role->name }}
                                </span>

                            @empty

                                <span class="text-muted">
                                    Belum ada role
                                </span>

                            @endforelse

                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="card border-0 shadow-sm mt-4">

    <div class="card-body">

        <h5 class="fw-bold">
            Selamat Datang
        </h5>

        <p class="text-muted mb-0">

            Anda berhasil masuk ke SSO Pemerintah
            Kabupaten Demak.

            Satu akun nantinya dapat digunakan untuk
            mengakses berbagai aplikasi yang terdaftar
            pada SSO.

        </p>

    </div>

</div>

@endsection