<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Events\EventService;
use App\Contracts\EventInterface;
use App\Contracts\AuthInterface;
use App\Services\Auth\AuthService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $services = [
            EventInterface::class => EventService::class,
            AuthInterface::class  => AuthService::class
        ];

        foreach ($services as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }

        $this->loadHelpers();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }


    private function loadHelpers()
    {
        $helperFile = app_path('Helpers/helpers.php');

        if (file_exists($helperFile)) {
            require_once $helperFile;
        }
    }
}
