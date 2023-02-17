<?php

namespace App\Objects\TypeHouse;


class Elite
{
    private const YES = 'yes';
    private const NO = 'no';

    public static function all(): array
    {
        return [
            self::YES => __("realty.yes"),
            self::NO => __("realty.no"),
        ];
    }

}
