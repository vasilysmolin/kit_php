<?php

namespace App\Http\Controllers\Import;

use App\Models\FoodRestaurantDishes;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FoodImport implements ToModel, WithStartRow
{

    public function startRow(): int
    {
        return 2;
    }


    /**
     * @param array $row
     * @return FoodRestaurantDishes
     */
    public function model(array $row): FoodRestaurantDishes
    {

        return new FoodRestaurantDishes([
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
