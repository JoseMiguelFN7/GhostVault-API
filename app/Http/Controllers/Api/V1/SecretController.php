<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SecretController extends Controller
{
    // Endpoint for creating a secret
    public function store(StoreSecretRequest $request)
    {
        $validated = $request->validated();

        $secret = new Secret();
        $secret->content = $validated['content'];
        $secret->requires_password = $validated['requires_password'] ?? false;
        $hours = $validated['expires_in_hours'] ?? 1;
        $secret->expires_at = now()->addHours($hours);
        $secret->save();
        
        return response()->json([
            'message' => 'Secreto creado con éxito',
            'uuid' => $secret->uuid,
            'url'  => url('/s/' . $secret->uuid),
            'requires_password' => $secret->requires_password,
            'expires_at' => $secret->expires_at,
        ], 201);
    }
}
