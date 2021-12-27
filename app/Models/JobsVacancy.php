<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobsVacancy extends Model
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
        'education',
        'experience',
        'schedule',
        'work_experience',
        'alias',
        'address',
        'profile_id',
        'category_id',
        'phone',
        'duties',
        'demands',
        'additionally',
        'min_price',
        'max_price',
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
