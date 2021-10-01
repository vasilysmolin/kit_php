<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'bool',
    ];

    public function regions()
    {
        return $this->hasMany(Region::class, 'country_id', 'id');
    }

    public function region()
    {
        return $this->hasOne(Region::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->hasOneThrough(City::class, Region::class);
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class, Region::class);
    }
}
