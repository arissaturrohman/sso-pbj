<?php

use Demak\SsoClient\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('sso-client.routes.middleware', ['web']))
    ->prefix(config('sso-client.routes.prefix', 'sso'))
    ->group(function () {
        Route::get('/redirect', [SsoController::class, 'redirect'])->name('sso.redirect');
        Route::get('/auto-login', [SsoController::class, 'redirect'])->name('sso.auto-login');
        Route::get('/callback', [SsoController::class, 'callback'])->name('sso.callback');
    });
