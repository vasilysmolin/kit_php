<?php

namespace App\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Realty extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;
    use SortableTrait;
    use Searchable;
    use CascadeDelete;

    protected $cascadeDeleteMorph = ['images'];

    protected $fillable = [
        'id',
        'external_id',
        'title',
        'reason',
        'name',
        'video',
        'state',
        'price',
        'price_per_square',
        'date_build',
        'cadastral_number',
        'ceiling_height',
        'sale_price',
        'description',
        'sort',
        'alias',
        'profile_id',
        'house_id',
        'city_id',
        'latitude',
        'longitude',
        'street',
        'house',
        'category_id',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the options for generating the slug.
     *
     * @return \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->doNotGenerateSlugsOnUpdate()
            ->slugsShouldBeNoLongerThan(10)
            ->generateSlugsFrom('description')
            ->saveSlugsTo('alias');
    }

    public function categories()
    {
        return $this->hasOne(RealtyCategory::class, 'id', 'category_id');
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function latestImage()
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    public function oldestImage()
    {
        return $this->morphOne(Image::class, 'imageable')->oldestOfMany();
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }

    public function parameters()
    {
        return $this->morphToMany(
            Parameter::class,
            'itemable',
            'filter_parameters'
        )->orderBy('parameters.sort');
    }

    public function agent()
    {
        return $this->morphOne(
            SubAgent::class,
            'itemable'
        );
    }

    public function house()
    {
        return $this->belongsTo(House::class, 'house_id', 'id');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->only(['name','description', 'state', 'street', 'sort']);
        $array['filter'] = $this->parameters->pluck('value')->join(', ');
        return $array;
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'realties';
    }
}
