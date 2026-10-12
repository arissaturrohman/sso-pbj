@extends('layouts.app')

@section('title', 'Otorisasi Aplikasi')

@section('content')

    <div class="row justify-content-center">

        <div class="col-lg-7 col-md-9">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">

                        <div class="mb-3">

                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;">
                            </i>

                        </div>

                        <h4 class="fw-bold">
                            Otorisasi Aplikasi
                        </h4>

                        <p class="text-muted mb-0">
                            Aplikasi berikut meminta akses ke akun SSO Anda.
                        </p>

                    </div>


                    <div class="border rounded p-4 mb-4">

                        <div class="d-flex align-items-center">

                            <div class="me-3">

                                <div
                                    class="bg-primary bg-opacity-10
                                        rounded-circle p-3">

                                    <i class="bi bi-grid text-primary fs-4"></i>

                                </div>

                            </div>

                            <div>

                                <h5 class="fw-bold mb-1">
                                    {{ $client->name }}
                                </h5>

                                <small class="text-muted">
                                    Aplikasi meminta akses menggunakan akun
                                    SSO Demak.
                                </small>

                            </div>

                        </div>

                    </div>


                    @if (count($scopes) > 0)

                        <div class="mb-4">

                            <h6 class="fw-bold">
                                Akses yang diminta:
                            </h6>

                            <ul class="list-group">

                                @foreach ($scopes as $scope)
                                    <li class="list-group-item">

                                        <i
                                            class="bi bi-check-circle
                                              text-success me-2">
                                        </i>

                                        {{ $scope->description }}

                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    @endif


                    <div class="alert alert-info">

                        <i class="bi bi-person-circle me-2"></i>

                        Anda login sebagai:

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                        <br>

                        <small>
                            {{ auth()->user()->email }}
                        </small>

                    </div>


                    <div class="d-flex gap-2 justify-content-end">

                        <form method="POST" action="{{ route('passport.authorizations.deny') }}">

                            @csrf
                            @method('DELETE')

                            <input type="hidden" name="state" value="{{ $request->state }}">

                            <input type="hidden" name="client_id" value="{{ $request->client_id }}">

                            <input type="hidden" name="auth_token" value="{{ $authToken }}">

                            <button type="submit" class="btn btn-outline-secondary">

                                Tolak

                            </button>

                        </form>


                        <form method="POST" action="{{ route('passport.authorizations.approve') }}">

                            @csrf

                            <input type="hidden" name="state" value="{{ $request->state }}">

                            <input type="hidden" name="client_id" value="{{ $request->client_id }}">

                            <input type="hidden" name="auth_token" value="{{ $authToken }}">

                            <button type="submit" class="btn btn-primary">

                                <i class="bi bi-check-lg me-1"></i>

                                Izinkan Akses

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
