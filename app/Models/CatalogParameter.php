<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'sort',
    ];

    public function filter(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(CatalogFilter::class, 'filter_id', 'id')
            ->orderBy('sort');
    }

    public function adParameters(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(
            CatalogAd::class,
            'catalog_ad_parameters',
            'parameter_id',
            'ad_id'
        );
    }
}
