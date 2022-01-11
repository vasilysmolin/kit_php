<?php

namespace App\Objects\Schedule\Constants;

use App\Objects\Schedule\Contract\ScheduleInterface;

class Schedule implements ScheduleInterface
{
    private $schedule = [
        'all_day' => 'Полный день',
        'remote_work' => 'Удаленная работа',
        'shift_method' => 'Вахтовый метод',
        'part_day' => 'Неполный день',
        'free_schedule' => 'свободный график',
        'shift_work' => 'сменный график',
    ];

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->key = $key ;
        $this->value = $value;
    }

    public function get(): array
    {
        return $this->schedule;
    }

    public function getById(): ?string
    {
        if (array_key_exists($this->key, $this->schedule)) {
            return $this->schedule[$this->key];
        } else {
            return null;
        }
    }

    public function parce()
    {
        switch ($this->key) {
            case 1:
                return 'all_day';
            case 2:
                return 'remote_work';
            case 3:
                return 'shift_method';
            case 4:
                return 'part_day';
            case 5:
                return 'free_schedule';
            case 6:
                return 'shift_work';
        }
    }
}
