<?php

namespace App\Objects\TypeHouse;


class DeadLine
{
    private const ONE = 'one';
    private const TWO = 'two';
    private const THREE = 'three';
    private const FOUR = 'four';
    private const FINISH = 'finish';

    public static function all(): array
    {
        return [
            self::ONE => __("realty.one"),
            self::TWO => __("realty.two"),
            self::THREE => __("realty.three"),
            self::FOUR => __("realty.four"),
            self::FINISH => __("realty.finish"),
        ];
    }

}
