<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemperatureReading extends Model
{
    protected $fillable = [
        'machine_id',
        'recorded_at',
        'temperature_c',
        'vibration',
        'pressure',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature_c' => 'float',
        'vibration' => 'float',
        'pressure' => 'float',
    ];

    /**
     * Get the machine that owns this temperature reading
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
