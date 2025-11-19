<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Secret;
use App\Http\Resources\SecretResource;
use App\Http\Requests\StoreSecretRequest;
use Illuminate\Support\Facades\DB;

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
            'requires_password' => $secret->requires_password,
            'expires_at' => $secret->expires_at,
        ], 201);
    }

    // Endpoint for retrieving and deleting a secret
    public function show(Secret $secret)
    {
        // Check if the secret has expired
        if ($secret->expires_at->isPast()) {
            
            // if expired, delete it and return a not found response
            $secret->delete(); 
            
            return response()->json([
                'message' => 'Este secreto ha expirado y fue destruido.'
            ], 404);
        }

        //burn-on-read: return the secret and delete it
        try {
            $dataToReturn = null;

            DB::transaction(function () use ($secret, &$dataToReturn) {
                
                //Prepare the data to return
                $dataToReturn = new SecretResource($secret);

                //Delete the secret from the database
                $secret->delete();

                //Delete files associated with the secret if any (TO BE IMPLEMENTED)

            }, 3); // Retry up to 3 times in case of deadlock

            //Return the secret data
            return $dataToReturn;
        } catch (\Exception $e) {
            // if any error occurs during the transaction, return an error response. The secret will remain in the database.
            return response()->json([
                'message' => 'No se pudo leer el secreto en este momento, por favor intente de nuevo.'
            ], 500);
        }
    }
}
