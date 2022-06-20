<?php

namespace App\Objects\TypeModules;

use App\Objects\Schedule\Contract\ScheduleInterface;
use Illuminate\Support\Collection;

class TypeModules implements ScheduleInterface
{
    private const JOB = 'job';
    private const CATALOG = 'catalog';
    private const SERVICE = 'service';

    private $states = [
        self::JOB => 'работа',
        self::CATALOG => 'каталог',
        self::SERVICE => 'услуги',
    ];

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function get(): array
    {
        return $this->states;
    }

    public function getById(): ?string
    {
        if (array_key_exists($this->key, $this->states)) {
            return $this->states[$this->key];
        } else {
            return null;
        }
    }

    public function isExists(string $value): bool
    {
        $key = array_key_exists($value, $this->states);
        return $key !== false;
    }

    public function stringKeys(): string
    {
        return collect($this->states)->keys()->join(',');
    }

    public function keys(): Collection
    {
        return collect($this->states)->keys();
    }

    public function job(): string
    {
        return self::JOB;
    }

    public function catalog(): string
    {
        return self::CATALOG;
    }

    public function service(): string
    {
        return self::SERVICE;
    }
}
