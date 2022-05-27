<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class City extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'id',
        'region_id',
        'prepositionalName',
        'name',
        'alias',
        'latitude',
        'longitude',
        'isMetro',
        'isDistrict',
        'title',
        'description',
        'seoH1',
        'text',
        'active',
    ];

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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        return $array;
    }

//    /**
//     * Get the name of the index associated with the model.
//     *
//     * @return string
//     */
//    public function searchableAs()
//    {
//        return 'cities_index';
//    }
}
