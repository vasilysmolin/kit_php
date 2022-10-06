<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealtyParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'sort',
    ];

    public function filter(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(RealtyFilter::class, 'filter_id', 'id')
            ->orderBy('sort');
    }

    public function realtyParameters(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(
            Realty::class,
            'realty_parameters',
            'parameter_id',
            'ad_id'
        );
    }
}
