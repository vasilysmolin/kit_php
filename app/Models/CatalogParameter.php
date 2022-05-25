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

    public function filter(): \Illuminate\Database\Eloquent\Relations\hasOne
    {
        return $this->hasOne(CatalogFilter::class, 'filter_id', 'id');
    }

    public function adParameters(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CatalogAd::class, 'filter_id', 'id');
    }
}
