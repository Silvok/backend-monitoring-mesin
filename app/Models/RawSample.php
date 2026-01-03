<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawSample extends Model
{
    protected $fillable = [
        'batch_id',
        'machine_id',
        't_ms',
        'ax_g',
        'ay_g',
        'az_g',
    ];

    protected $casts = [
        't_ms' => 'integer',
        'ax_g' => 'float',
        'ay_g' => 'float',
        'az_g' => 'float',
    ];

    /**
     * Get the machine that owns this sample
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
