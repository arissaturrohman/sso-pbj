<?php

namespace Demak\SsoClient;

use Demak\SsoClient\Contracts\SsoUserResolver;
use Illuminate\Support\ServiceProvider;

class SsoClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sso-client.php', 'sso-client');

        $this->app->bind(SsoUserResolver::class, function () {
            $class = config('sso-client.resolver', Resolvers\DefaultUserResolver::class);

            return app($class);
        });
    }

    public function boot(): void
    {
        if (config('sso-client.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sso-client');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/sso-client.php' => config_path('sso-client.php'),
            ], 'sso-client-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/sso-client'),
            ], 'sso-client-views');

            $this->publishes([
                __DIR__ . '/../stubs/CustomResolver.stub' => app_path('Sso/CustomResolver.php'),
            ], 'sso-client-resolver');
        }
    }
}
