<?php

namespace App\Objects\Time\Contract;

interface TimeInterface
{
    public function get(): array;
    public function getById(): ?string;
}
