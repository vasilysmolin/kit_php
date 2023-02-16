<?php

namespace App\Objects\TypeHouse;


class TypeHouse
{
    private const PANEL = 'item';
    private const BRICK = 'brick';
    private const WOOD = 'wood';
    private const CINDER = 'cinder';
    private const MONOLITIC = 'monolithic';
    private const BLOCK = 'block';

    public static function all(): array
    {
        return [
            self::PANEL => __("realty.panel"),
            self::BRICK => __("realty.brick"),
            self::WOOD => __("realty.wood"),
            self::CINDER => __("realty.cinder"),
            self::MONOLITIC => __("realty.monolithic"),
            self::BLOCK => __("realty.block"),
        ];
    }

}
