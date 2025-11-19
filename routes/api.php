<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SecretController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    
    //Endpoint for creating a secret
    Route::post('secrets', [SecretController::class, 'store']);
    //Endpoint for retrieving a secret
    Route::get('secrets/{secret}', [SecretController::class, 'show']);
});
