<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machine extends Model
{
    protected $fillable = [
        'name',
        'location',
        'threshold_warning',
        'threshold_critical',
        'motor_power_hp',
        'motor_rpm',
        'iso_class',
    ];

    protected $casts = [
        'threshold_warning' => 'decimal:2',
        'threshold_critical' => 'decimal:2',
        'motor_power_hp' => 'decimal:2',
        'motor_rpm' => 'integer',
    ];

    /**
     * Get threshold config for this machine
     */
    public function getThresholdConfig(): array
    {
        return [
            'warning' => (float) ($this->threshold_warning ?? 1.8),
            'critical' => (float) ($this->threshold_critical ?? 4.5),
        ];
    }

    /**
     * Get severity level based on RMS value
     */
    public function getSeverityLevel(float $rms): string
    {
        if ($rms >= $this->threshold_critical) {
            return 'critical';
        } elseif ($rms >= $this->threshold_warning) {
            return 'warning';
        }
        return 'normal';
    }

    /**
     * Get severity label based on RMS value
     */
    public function getSeverityLabel(float $rms): string
    {
        if ($rms >= $this->threshold_critical) {
            return 'Bahaya';
        } elseif ($rms >= $this->threshold_warning) {
            return 'Peringatan';
        }
        return 'Normal';
    }

    /**
     * Get all raw samples for this machine
     */
    public function rawSamples(): HasMany
    {
        return $this->hasMany(RawSample::class);
    }

    /**
     * Get all analysis results for this machine
     */
    public function analysisResults(): HasMany
    {
        return $this->hasMany(AnalysisResult::class);
    }

    /**
     * Get latest analysis result
     */
    public function latestAnalysis()
    {
        return $this->hasOne(AnalysisResult::class)->latestOfMany();
    }
}
