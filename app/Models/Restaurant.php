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
    ];

    public function restaurantFood()
    {
        return $this->hasMany(RestaurantFood::class,'restaurant_id','id');
    }

    public function uploads()
    {
        return $this->belongsToMany(UploadFiles::class,'restaurant_uploads','restaurant_id','upload_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function categoryFood()
    {
        return $this->belongsTo(CategoryFood::class,'category_id','id');
    }

    public function image()
    {
        return $this->morphOne(UploadFiles::class, 'imageable');
    }
}
