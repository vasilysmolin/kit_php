<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class House extends Model
{
    use HasFactory;
    use HasSlug;
    use SortableTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'alias',
        'description',
        'deadline',
        'street',
        'house',
        'parking',
        'ceiling_height',
        'elite',
        'reason',
        'date_build',
        'total_floors',
        'type',
        'finishing',
        'city_id',
        'latitude',
        'longitude',
        'agent_id',
        'profile_id',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->doNotGenerateSlugsOnUpdate()
            ->slugsShouldBeNoLongerThan(10)
            ->generateSlugsFrom('name')
            ->saveSlugsTo('alias');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function agent()
    {
        return $this->morphOne(
            SubAgent::class,
            'itemable'
        );
    }

    public function realties()
    {
        return $this->hasMany(NewBuild::class, 'house_id', 'id');
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
}
