<?php

namespace App\Objects\TypeService;

use App\Objects\Schedule\Contract\ScheduleInterface;
use Illuminate\Support\Collection;

class TypeService implements ScheduleInterface
{
    private const MY = 'my';
    private const NEED = 'need';

    private $types = [
        self::MY => 'Я предлагаю',
        self::NEED => 'Хочу найти',
    ];

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function get(): array
    {
        return $this->types;
    }

    public function getById(): ?string
    {
        if (array_key_exists($this->key, $this->types)) {
            return $this->types[$this->key];
        } else {
            return null;
        }
    }

    public function isExists(string $value): bool
    {
        $key = array_key_exists($value, $this->types);
        return $key !== false;
    }

    public function stringKeys(): string
    {
        return collect($this->types)->keys()->join(',');
    }

    public function keys(): Collection
    {
        return collect($this->types)->keys();
    }

    public function my(): string
    {
        return self::MY;
    }

    public function need(): string
    {
        return self::NEED;
    }
}
