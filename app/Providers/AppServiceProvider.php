<?php

namespace App\Providers;

use App\Models\Passport\Client;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\CarbonInterval;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Passport::useClientModel(
            Client::class
        );

        Passport::authorizationView(
            'auth.oauth.authorize'
        );

        Passport::tokensCan([
            'user:read' => 'Melihat informasi dasar pengguna',
        ]);

        Passport::defaultScopes([
            'user:read',
        ]);

        Passport::tokensExpireIn(
            CarbonInterval::hours(1)
        );

        Passport::refreshTokensExpireIn(
            CarbonInterval::days(30)
        );

        Passport::personalAccessTokensExpireIn(
            CarbonInterval::months(6)
        );
    }
}
