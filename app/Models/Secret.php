<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Secret extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'requires_password',
        'expires_at',
    ];

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'requires_password' => 'boolean',
        'expires_at' => 'datetime',
    ];

    // Automatically generate UUID on creation
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Use UUID for route model binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
