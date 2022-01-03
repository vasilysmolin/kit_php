<?php

namespace App\Objects;

use Illuminate\Support\Collection;

class JsonHelper
{

    public function getIndexStructure($model, Collection $collection, int $count, int $skip): array
    {

        return [
            'meta' => [
                'skip' => $skip ?? 0,
                'limit' => config('settings.take_twenty_five'),
                'total' => $count ?? 0,
            ],
            $model->getTable() => $collection,
        ];
    }

}
