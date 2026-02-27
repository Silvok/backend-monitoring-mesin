<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'is_protected',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_protected' => 'boolean',
    ];
}
