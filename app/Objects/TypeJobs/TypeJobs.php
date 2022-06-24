<?php

namespace App\Objects\TypeJobs;

use App\Objects\Schedule\Contract\ScheduleInterface;
use Illuminate\Support\Collection;

class TypeJobs implements ScheduleInterface
{
    private const MY = 'my';
    private const NEED = 'need';

    private $types = [
        self::MY => 'Моё резюме',
        self::NEED => 'Мне нужен',
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
