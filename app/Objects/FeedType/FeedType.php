<?php

namespace App\Objects\FeedType;

class FeedType
{
    private const CIAN = 'cian';
    private const YANDEX = 'yandex';
    private const AVITO = 'avito';

    private const KEYS = [
        self::CIAN,
        self::YANDEX,
        self::AVITO,
    ];

    public static function all(): array
    {
        return [
            self::CIAN => __('feed.cian'),
            self::YANDEX => __('feed.yandex'),
            self::AVITO => __('feed.avito'),
        ];
    }
    public static function keys(): array
    {
        return self::KEYS;
    }
    public static function cian(): string
    {
        return self::CIAN;
    }
    public static function yandex(): string
    {
        return self::YANDEX;
    }
    public static function avito(): string
    {
        return self::AVITO;
    }
}
