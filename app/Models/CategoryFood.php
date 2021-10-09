<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFood extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'bool',
    ];

    protected $fillable = [
        'name',
        'alias',
        'active',
        'sort',
    ];

    public function restaurantFood()
    {
        return $this->hasMany(RestaurantFood::class, 'category_id', 'id');
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
