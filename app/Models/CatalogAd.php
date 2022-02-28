<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'name',
        'state',
        'price',
        'sale_price',
        'description',
        'sort',
        'alias',
        'address',
        'profile_id',
        'category_id',
    ];

    public function categories()
    {
        return $this->hasOne(CatalogAdCategory::class, 'id', 'category_id');
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
}
