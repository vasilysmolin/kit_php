<?php

namespace App\Objects\SalaryType\Contract;

interface SalaryTypeInterface
{
    public function get(): array;
    public function getById(): ?string;
}
