<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckToken;

Route::middleware([
    'auth:api',
    CheckToken::using('user:read'),
])->get('/user', function (Request $request) {

    $user = $request->user();

    return response()->json([
        'success' => true,

        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'nip' => $user->nip,
            'email' => $user->email,
            'status' => $user->status,

            'roles' => $user
                ->getRoleNames()
                ->values(),

            'permissions' => $user
                ->getAllPermissions()
                ->pluck('name')
                ->values(),
        ],
    ]);
});
