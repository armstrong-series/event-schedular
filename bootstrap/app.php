<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


if (!function_exists('mapRoutes')) {
    function mapRoutes()
    {
        $routes = [
            'auth'     => '/../routes/auth/auth.php',
            'events'  => '/../routes/events/events.php'
        
        ];

        foreach ($routes as $prefix => $routeFile) {
            Route::prefix($prefix === 'default' ? '' : $prefix)->group(function () use ($routeFile) {
                require __DIR__ . $routeFile;
            });
        }
    }
}


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->group(function () {
                    mapRoutes();
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e): JsonResponse {
            return glimpseResponse([], 401, $e->getMessage(), false); 
        });
        $exceptions->render(function (AuthorizationException $e): JsonResponse {
            return glimpseResponse([], 403, $e->getMessage(), false);   
        });
    })->create();
