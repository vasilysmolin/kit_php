<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'alias',
        'user_id',
        'category_id',
        'title',
        'description',
        'min_delivery_price',
        'street',
        'house',
        'coords',
        'phone',
        'email',
    ];
    protected $casts = [
        'active' => 'bool',
        'min_delivery_price' => 'int',
    ];

    public function categoryRestaurant()
    {
        return $this->belongsTo(CategoryRestaurant::class, 'category_id', 'id');
    }

    public function restaurantFood()
    {
        return $this->hasMany(RestaurantFood::class, 'restaurant_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
