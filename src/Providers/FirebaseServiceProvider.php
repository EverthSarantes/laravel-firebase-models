<?php

namespace Firebase\Providers;

use Illuminate\Support\ServiceProvider;
use Firebase\Console\Commands\MakeFirebaseModel;
use Firebase\Auth\Guards\FirebaseGuard;
use Firebase\Auth\Providers\FirebaseUserProvider;
use Illuminate\Support\Facades\Auth;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Cargar la configuración del paquete
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/firebase.php', 'firebase'
        );

        $this->commands([
            MakeFirebaseModel::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('firebase', function ($app, $name, array $config) {
            return new FirebaseGuard(Auth::createUserProvider($config['provider']));
        });

        Auth::provider('firebase', function ($app, array $config) {
            return new FirebaseUserProvider();
        });

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');
    }
}
