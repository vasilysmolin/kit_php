<?php

namespace App\Objects\Time\Contract;

interface TimeInterface
{
    public function arrTimes(): array;
    public function getTime(): ?string;
}
