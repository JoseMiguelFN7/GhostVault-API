<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\CheckApiKey;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.key' => CheckApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom API 404 handler
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            
            // if the request is for the API
            if ($request->is('api/*')) {
                
                // Return a JSON response for API 404 errors
                return response()->json([
                    'message' => 'Resource not found.'
                ], 404);
            }
        });
    })->create();
