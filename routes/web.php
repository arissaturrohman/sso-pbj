<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        LoginController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login'
    ])->name('login.process');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');

    // Dashboard SSO: pusat akses semua aplikasi client
    Route::get('/apps', [
        \App\Http\Controllers\Auth\DashboardController::class,
        'index'
    ])->name('sso.apps');

    Route::post('/logout', [
        LoginController::class,
        'logout'
    ])->name('logout');
});



Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('permission:users.create')
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:users.create')
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.edit')
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.edit')
            ->name('users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete')
            ->name('users.destroy');

        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->middleware('permission:users.reset-password')
            ->name('users.reset-password');

        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->middleware('permission:users.toggle-status')
            ->name('users.toggle-status');


        /*
        |--------------------------------------------------------------------------
        | Role Management
        |--------------------------------------------------------------------------
        */

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view')
            ->name('roles.index');

        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create')
            ->name('roles.create');

        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create')
            ->name('roles.store');

        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.edit')
            ->name('roles.edit');

        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.edit')
            ->name('roles.update');

        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')
            ->name('roles.destroy');


        /*
|--------------------------------------------------------------------------
| Application Management
|--------------------------------------------------------------------------
*/

        Route::get('/applications', [ApplicationController::class, 'index'])
            ->middleware('permission:applications.view')
            ->name('applications.index');

        Route::get('/applications/create', [ApplicationController::class, 'create'])
            ->middleware('permission:applications.create')
            ->name('applications.create');

        Route::post('/applications', [ApplicationController::class, 'store'])
            ->middleware('permission:applications.create')
            ->name('applications.store');

        Route::get('/applications/{application}/edit', [ApplicationController::class, 'edit'])
            ->middleware('permission:applications.edit')
            ->name('applications.edit');

        Route::put('/applications/{application}', [ApplicationController::class, 'update'])
            ->middleware('permission:applications.edit')
            ->name('applications.update');

        Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])
            ->middleware('permission:applications.delete')
            ->name('applications.destroy');

        Route::post('/applications/{application}/toggle-status', [ApplicationController::class, 'toggleStatus'])
            ->middleware('permission:applications.edit')
            ->name('applications.toggle-status');

        // Route::post('/applications/{application}/regenerate-secret', [ApplicationController::class, 'regenerateSecret'])
        //     ->middleware('permission:applications.edit')
        //     ->name('applications.regenerate-secret');
    });



Route::get('/oauth/test', function (Request $request) {

    $state = Str::random(40);

    $codeVerifier = Str::random(64);

    $encoded = base64_encode(
        hash('sha256', $codeVerifier, true)
    );

    $codeChallenge = strtr(
        rtrim($encoded, '='),
        '+/',
        '-_'
    );

    session([
        'oauth_state' => $state,
        'oauth_code_verifier' => $codeVerifier,
    ]);

    $query = http_build_query([
        'client_id' => env('OAUTH_TEST_CLIENT_ID'),
        'redirect_uri' => env('OAUTH_TEST_REDIRECT_URI'),
        'response_type' => 'code',
        'scope' => 'user:read',
        'state' => $state,
        'code_challenge' => $codeChallenge,
        'code_challenge_method' => 'S256',
    ]);

    return redirect(
        '/oauth/authorize?' . $query
    );
});




Route::get('/oauth/test/callback', function (Request $request) {

    // 1. Tampilkan error asli dari server OAuth
    if ($request->has('error')) {
        return response()->json([
            'success'           => false,
            'error'             => $request->query('error'),
            'error_description' => $request->query('error_description'),
            'hint'              => $request->query('hint'),
            'query'             => $request->query(),
        ], 400);
    }

    $state        = session()->pull('oauth_state');
    $codeVerifier = session()->pull('oauth_code_verifier');

    // 2. Validasi state (aman terhadap null)
    if (
        !$state ||
        !is_string($request->state) ||
        !hash_equals($state, $request->state)
    ) {
        abort(419, 'Invalid OAuth state atau session hilang.');
    }

    // 3. Pastikan code ada
    if (!$request->filled('code')) {
        abort(400, 'Authorization code tidak ditemukan.');
    }

    // 4. Tukar code dengan token
    $tokenResponse = Http::asForm()->post(url('/oauth/token'), [
        'grant_type'    => 'authorization_code',
        'client_id'     => config('services.oauth_test.client_id'),
        'redirect_uri'  => config('services.oauth_test.redirect_uri'),
        'code_verifier' => $codeVerifier,
        'code'          => $request->code,
    ]);

    if (!$tokenResponse->successful()) {
        return response()->json([
            'success'        => false,
            'token_response' => $tokenResponse->json(),
        ], $tokenResponse->status());
    }

    $tokens = $tokenResponse->json();

    $userResponse = Http::withToken($tokens['access_token'])
        ->acceptJson()
        ->get(url('/api/user'));

    return response()->json([
        'success' => true,
        'tokens'  => [
            'token_type'    => $tokens['token_type'] ?? null,
            'expires_in'    => $tokens['expires_in'] ?? null,
            'refresh_token' => isset($tokens['refresh_token']),
        ],
        'user' => $userResponse->json(),
    ]);
});
