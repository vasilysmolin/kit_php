<?php

namespace App\Objects\Schedule\Contract;

interface ScheduleInterface
{
    public function get(): array;
    public function getById(): ?string;
}
