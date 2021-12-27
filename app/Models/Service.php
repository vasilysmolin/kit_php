<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'name',
        'price',
        'description',
        'longitude',
        'latitude',
        'sort',
        'consultation',
        'alias',
        'address',
        'profile_id',
        'category_id',
        'phone',
        'hourly_payment',
        'guarantee',
        'additionally',
        'contract',
        'work_experience',
    ];

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(JobsVacancyCategory::class, 'id', 'category_id');
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
