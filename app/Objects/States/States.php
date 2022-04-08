<?php

namespace App\Objects\States;

use App\Objects\Schedule\Contract\ScheduleInterface;

class States implements ScheduleInterface
{
    private $states = [
        'new' => 'Новый',
        'in_progress' => 'На проверке',
        'block' => 'Заблокирован',
        'active' => 'Активный',
        're_block' => 'Повторно заблокирован',
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
}
