<?php

namespace App\Objects\Reasons;

class Reasons
{
    private const PHOTO = 'photo';
    private const CONTENT = 'content';
    private const TEXT = 'text';
    private const PRICE = 'price';

    private $states = [
        self::PHOTO => 'Отсутствует фотография',
        self::CONTENT => 'Запрещенный контент (фото или текст)',
        self::TEXT => 'Некорректный текст обьявления',
        self::PRICE => 'Возможно, вы ошиблись в указании цены',
    ];

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->key = $key ;
        $this->value = $value;
    }

    public function get(): array
    {
        return $this->states;
    }

    public function getById($key): ?string
    {
        if (array_key_exists($key, $this->states)) {
            return $this->states[$key];
        } else {
            return null;
        }
    }

    public function isExists(string $value): bool
    {
        $key = array_key_exists($value, $this->states);
        return $key !== false;
    }

    public function keys(): string
    {
        return collect($this->states)->keys()->join(',');
    }

    public function photo(): string
    {
        return self::PHOTO;
    }

    public function content(): string
    {
        return self::CONTENT;
    }

    public function price(): string
    {
        return self::PRICE;
    }

    public function text(): string
    {
        return self::TEXT;
    }
}
