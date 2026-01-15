<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FFTResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'analysis_result_id',
        'frequencies',
        'amplitudes',
    ];

    protected $casts = [
        'frequencies' => 'array',
        'amplitudes' => 'array',
    ];

    public function analysisResult()
    {
        return $this->belongsTo(AnalysisResult::class);
    }
}
