<?php

namespace App\Http\Controllers\Import;


use App\Models\RestaurantFood;
use Maatwebsite\Excel\Concerns\ToModel;

class FoodImport implements ToModel
{

    public function model(array $row)
    {
        return new RestaurantFood([
            'name'     => $row[0],
            'alias'    => $row[1],
            'restaurant_id' => $row[2],
            'category_id' => $row[3],
            'description' => $row[4],
            'price' => $row[5],
            'salePrice' => $row[6],
            'quantity' => $row[7],
            'popular' => $row[8],
            'sale' => $row[9],
            'novetly' => $row[10],
        ]);
    }
}
