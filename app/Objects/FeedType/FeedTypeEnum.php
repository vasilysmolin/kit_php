<?php

namespace App\Objects\FeedType;

use phpDocumentor\Reflection\DocBlock\Tags\Throws;

enum FeedTypeEnum: string
{
    case Cian = 'cian';
    case Yandex = 'yandex';
    case Avito = 'avito';

    public static function all(): array
    {
        return [
            FeedTypeEnum::Cian->value => __('feed.cian'),
            FeedTypeEnum::Yandex->value => __('feed.yandex'),
            FeedTypeEnum::Avito->value => __('feed.avito'),
        ];
    }
    public static function keys(): array
    {
        return array_keys(self::all());
    }
    public static function find($key): FeedTypeEnum
    {
        if (array_key_exists($key, self::all())) {
            return FeedTypeEnum::$key();
        }
        throw new \Exception();
    }
    public static function cian(): FeedTypeEnum
    {
        return FeedTypeEnum::Cian;
    }
    public static function yandex(): FeedTypeEnum
    {
        return FeedTypeEnum::Yandex;
    }
    public static function avito(): FeedTypeEnum
    {
        return FeedTypeEnum::Avito;
    }
}
