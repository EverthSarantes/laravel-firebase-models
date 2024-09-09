<?php

namespace Firebase\Providers;

use Illuminate\Support\ServiceProvider;
use Firebase\Console\Commands\MakeFirebaseModel;

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
        // $this->publishes([
        //     __DIR__ . '/../Config/firebase.php' => config_path('firebase.php'),
        // ], 'firebase-config');
    }
}
