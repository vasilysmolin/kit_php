<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobsVacancyCategory extends Model
{
    use HasFactory;

    public $table = 'jobs_vacancy_categories';

    protected $fillable = [
        'name',
        'alias',
        'active',
        'sort',
    ];

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
