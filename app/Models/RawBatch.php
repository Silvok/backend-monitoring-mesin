<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawBatch extends Model
{
    protected $table = 'raw_batches';

    protected $fillable = [
        'machine_id',
        'captured_at',
        'name',
        'batch_time',
        // tambahkan field lain sesuai kebutuhan
    ];

    public $timestamps = true;
}
