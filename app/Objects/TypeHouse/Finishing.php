<?php

namespace App\Objects\TypeHouse;


class Finishing
{
    private const WITH = 'with';
    private const WITHOUT = 'without';

    public static function all(): array
    {
        return [
            self::WITH => __("realty.with"),
            self::WITHOUT => __("realty.without"),
        ];
    }

}
