<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecretResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Load files
        $this->resource->load('files');

        // Read files from storage
        $filesArray = $this->resource->files->map(function ($file) {
            try {
                // Read file content
                $fileContent = \Illuminate\Support\Facades\Storage::get($file->storage_path);
                
                return [
                    'encrypted_name' => $file->encrypted_name,
                    'file_data' => base64_encode($fileContent),
                ];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to read file {$file->storage_path}: " . $e->getMessage());
                return null;
            }
        })->filter();

        return [
            'content' => $this->resource->content,
            'requires_password' => $this->resource->requires_password,
            'files' => $filesArray->values()->toArray(),
        ];
    }
}
