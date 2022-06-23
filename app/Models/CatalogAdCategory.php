<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class CatalogAdCategory extends Model
{
    use HasFactory;
    use Searchable;

    public $table = 'catalog_ad_categories';

    protected $fillable = [
        'name',
        'description',
        'color_id',
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
        return $this->hasMany(CatalogAdCategory::class, 'parent_id', 'id')
            ->with(['categories.color', 'categoriesParent.color']);
    }

    public function filters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CatalogFilter::class, 'category_id', 'id')
            ->orderBy('sort');
    }

    public function color(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id', 'id');
    }

    public function categoriesParent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CatalogAdCategory::class, 'id', 'parent_id')
            ->with('categoriesParent');
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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->only(['name','active']);
        return $array;
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'catalog_ad_categories';
    }
}
