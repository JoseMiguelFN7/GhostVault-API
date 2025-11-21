<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the API key from the request header
        $apiKey = $request->header('X-API-KEY');

        // Validate the API key
        $validApiKey = env('GHOSTVAULT_API_KEY');

        if (! $validApiKey || $apiKey !== $validApiKey) {
            
            // Return a 401 Unauthorized response if the API key is invalid or missing
            return response()->json([
                'message' => 'Acceso denegado. API Key inválida o ausente.'
            ], 401);
        }

        return $next($request);
    }
}
