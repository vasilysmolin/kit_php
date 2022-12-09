<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'sort',
        'active',
        'name',
        'alias',
        'category_id',
        'categoryable_id',
        'categoryable_type',
        'type',
    ];

    public function categoryable()
    {
        return $this->morphTo();
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\hasOne
    {
        return $this->hasOne(RealtyCategory::class, 'category_id', 'id');
    }

    public function parameters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Parameter::class, 'filter_id', 'id')
            ->orderBy('sort');
    }
}
