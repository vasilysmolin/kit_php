<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodDishesCategory extends Model
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

    protected $hidden = [
        'active',
        'updated_at',
        'created_at',
    ];

    public function dishes()
    {
        return $this->hasMany(FoodRestaurantDishes::class, 'category_id', 'id');
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
