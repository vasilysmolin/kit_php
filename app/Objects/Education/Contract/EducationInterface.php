<?php

namespace App\Objects\Education\Contract;

interface EducationInterface
{
    public function get(): array;
    public function getById(): ?string;
}
