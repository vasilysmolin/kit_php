<?php

namespace App\Objects\States;

use App\Objects\Schedule\Contract\ScheduleInterface;

class States implements ScheduleInterface
{
    private const NEW = 'new';
    private const IN_PROGRESS = 'in_progress';
    private const BLOCK = 'block';
    private const ACTIVE = 'active';
    private const RE_BLOCK = 're_block';
    private const PAUSE = 'pause';

    private $states = [
        self::NEW => 'Новый',
        self::IN_PROGRESS => 'На проверке',
        self::BLOCK => 'Заблокирован',
        self::ACTIVE => 'Активный',
        self::RE_BLOCK => 'Повторно заблокирован',
        self::PAUSE => 'На паузе',
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

    public function keys(): string
    {
        return collect($this->states)->keys()->join(',');
    }

    public function new(): string
    {
        return self::NEW;
    }

    public function inProgress(): string
    {
        return self::IN_PROGRESS;
    }

    public function active(): string
    {
        return self::ACTIVE;
    }

    public function block(): string
    {
        return self::BLOCK;
    }

    public function reBlock(): string
    {
        return self::RE_BLOCK;
    }

    public function pause(): string
    {
        return self::PAUSE;
    }

    public function hasChange($state): bool
    {
        $key = array_search($state, [self::PAUSE, self::ACTIVE], true);
        return $key !== false;
    }
}
