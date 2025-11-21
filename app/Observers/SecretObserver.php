<?php

namespace App\Observers;

use App\Models\Secret;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SecretObserver
{
    // Delete physical files when secret is deleted
    public function deleting(Secret $secret): void
    {
        // Load files if needed
        if (!$secret->relationLoaded('files')) {
            $secret->load('files');
        }

        // Delete each file from disk
        foreach ($secret->files as $file) {
            try {
                if (Storage::exists($file->storage_path)) {
                    Storage::delete($file->storage_path);
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete file {$file->storage_path}: " . $e->getMessage());
            }
        }
    }
}
