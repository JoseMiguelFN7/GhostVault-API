<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'secret_id',
        'encrypted_name',
        'storage_path'
    ];

    protected $hidden = [
        'id',
        'secret_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // A file belongs to a secret
    public function secret(): BelongsTo
    {
        return $this->belongsTo(Secret::class);
    }
}
