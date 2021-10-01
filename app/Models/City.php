<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'bool',
    ];


    public function region()
    {
        return $this->hasOne(Region::class,'id','region_id');
    }

}
