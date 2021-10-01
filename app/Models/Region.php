<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'bool',
    ];

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'region_id', 'id');
    }

    public function city()
    {
        return $this->hasOne(City::class, 'region_id', 'id');
    }
}
