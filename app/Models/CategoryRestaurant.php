<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryRestaurant extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'bool',
    ];

    public function restaurant()
    {
        return $this->hasMany(Restaurant::class,'category_id','id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
