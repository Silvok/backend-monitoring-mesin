<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisResult extends Model
{
    protected $fillable = [
        'batch_id',
        'machine_id',
        'fs_hz',
        'n',
        'rms',
        'peak_amp',
        'dominant_freq_hz',
        'mean',
        'std',
        'status',
        'condition_status',
        'error_message',
    ];

    protected $casts = [
        'fs_hz' => 'float',
        'n' => 'integer',
        'rms' => 'float',
        'peak_amp' => 'float',
        'dominant_freq_hz' => 'float',
        'mean' => 'float',
        'std' => 'float',
    ];

    /**
     * Get the machine that owns this analysis result
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Check if the result indicates an anomaly
     */
    public function isAnomalous(): bool
    {
        return in_array(strtolower($this->condition_status), ['anomaly', 'warning', 'critical']);
    }

    /**
     * Check if analysis was successful
     */
    public function isSuccessful(): bool
    {
        return strtolower($this->status) === 'success';
    }
}
