<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machine extends Model
{
    protected $fillable = [
        'name',
        'location',
    ];

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
