<?php

namespace App\Objects\SalaryType\Constants;

use App\Objects\SalaryType\Contract\SalaryTypeInterface;

class SalaryType implements SalaryTypeInterface
{
    private array $salaryType = [
        'salary' => 'Оклад',
        'salary_and_percent' => 'Оклад и проценты',
        'percent' => 'Только проценты',
    ];

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->key = $key ;
        $this->value = $value;
    }

    public function get(): array
    {
        return $this->salaryType;
    }

    public function getById(): ?string
    {
        if (array_key_exists($this->key, $this->salaryType)) {
            return $this->salaryType[$this->key];
        } else {
            return null;
        }
    }

    public function parce()
    {
        switch ($this->key) {
            case 1:
                return 'salary';
            case 2:
                return 'salary_and_percent';
            case 3:
                return 'percent';
        }
    }
}
