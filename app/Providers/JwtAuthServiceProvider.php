<?php //Creamos este provider con "php artisan make provider JwtAuthServiceProvider" para que funcione es necesario agregarlo en config/app.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JwtAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path()."/Helpers/JwtAuth.php"; //app_path redirige a la carpeta principal del proyecto
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
