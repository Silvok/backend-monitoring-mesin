<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawSample3Axis extends Model
{
    protected $table = 'raw_samples_3axis';

    protected $fillable = [
        'machine_id',
        'batch_id',
        't_ms',
        'ax_g',
        'ay_g',
        'az_g',
    ];

    public $timestamps = true;
}
