<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogAdCategory extends Model
{
    use HasFactory;

    public $table = 'catalog_ad_categories';

    protected $fillable = [
        'name',
        'alias',
        'active',
        'sort',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CatalogAdCategory::class, 'parent_id', 'id')->with('categories');
    }

    public function childrenCategories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CatalogAdCategory::class, 'parent_id', 'id')
            ->with('categories');
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function image(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function latestImage(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    public function oldestImage(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->oldestOfMany();
    }
}
