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
    // Create a new secret
    public function store(StoreSecretRequest $request)
    {
        $validated = $request->validated();

        $secret = new Secret();
        $secret->content = $validated['content'];
        $secret->requires_password = $validated['requires_password'] ?? false;
        $hours = $validated['expires_in_hours'] ?? 1;
        $secret->expires_at = now()->addHours($hours);
        $secret->save();

        // Store attached files if any
        if (isset($validated['files']) && is_array($validated['files'])) {
            foreach ($validated['files'] as $fileData) {
                try {
                    // Sanitize filename for safe storage
                    $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $fileData['encrypted_name']);
                    $filename = $sanitizedName . '.dat';
                    $storagePath = "secrets/{$filename}";

                    // Store file as Base64 string
                    \Illuminate\Support\Facades\Storage::put($storagePath, $fileData['file_data']);

                    // Save metadata to database
                    $secret->files()->create([
                        'encrypted_name' => $fileData['encrypted_name'],
                        'storage_path' => $storagePath,
                    ]);
                } catch (\Exception $e) {
                    // Log error but continue processing other files
                    \Illuminate\Support\Facades\Log::error('Error storing file: ' . $e->getMessage());
                }
            }
        }
        
        return response()->json([
            'message' => 'Secret created successfully',
            'uuid' => $secret->uuid,
            'requires_password' => $secret->requires_password,
            'expires_at' => $secret->expires_at,
        ], 201);
    }

    // Retrieve and delete secret (burn-on-read)
    public function show(Secret $secret)
    {
        // Check expiration
        if ($secret->expires_at->isPast()) {
            
            // Delete if expired
            $secret->delete(); 
            
            return response()->json([
                'message' => 'Secret has expired and was destroyed'
            ], 404);
        }

        // Burn-on-read: return content then delete
        try {
            $responseData = null;

            DB::transaction(function () use ($secret, &$responseData) {
                
                // Read files before deletion
                $secret->load('files');
                
                // Load files from storage
                $filesArray = $secret->files->map(function ($file) {
                    try {
                        // File is already Base64
                        if (\Illuminate\Support\Facades\Storage::exists($file->storage_path)) {
                            $fileContent = \Illuminate\Support\Facades\Storage::get($file->storage_path);
                            
                            return [
                                'encrypted_name' => $file->encrypted_name,
                                'file_data' => $fileContent,
                            ];
                        }
                        return null;
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to read file {$file->storage_path}: " . $e->getMessage());
                        return null;
                    }
                })->filter()->values()->toArray();

                // Prepare response data
                $responseData = [
                    'content' => $secret->content,
                    'requires_password' => $secret->requires_password,
                    'files' => $filesArray,
                ];

                // Delete secret (Observer handles files)
                $secret->delete();

            }, 3);

            // Return secret data
            return response()->json($responseData);
        } catch (\Exception $e) {
            // Error handling
            return response()->json([
                'message' => 'Unable to read secret, please try again'
            ], 500);
        }
    }
}
