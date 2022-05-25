<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'sort',
        'active',
        'name',
        'alias',
        'category_id',
    ];

    public function category(): \Illuminate\Database\Eloquent\Relations\hasOne
    {
        return $this->hasOne(CatalogAdCategory::class, 'category_id', 'id');
    }

    public function parameters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CatalogParameter::class, 'filter_id', 'id');
    }
}
