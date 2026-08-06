<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = [
        'cve_ent',
        'nomgeo',
        'pob_total',
    ];

    protected $casts = [
        'pob_total' => 'integer',
    ];
}
