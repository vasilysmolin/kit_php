<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class City extends Model
{
    use HasFactory;
    use Searchable;

    protected $casts = [
        'active' => 'bool',
    ];

    public function region()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');
    }

    public function timezone()
    {
        return $this->hasOne(Timezone::class, 'id', 'timezone_id');
    }
}
