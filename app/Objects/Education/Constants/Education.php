<?php

namespace App\Objects\Education\Constants;

use App\Objects\Education\Contract\EducationInterface;

class Education implements EducationInterface
{
    private $educations = [
        'higher' => 'Высшее',
        'incomplete_higher' => 'Незаконченное высшее',
        'secondary' => 'Среднее',
        'incomplete_secondary' => 'Среднее специальное',
    ];

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->key = $key ;
        $this->value = $value;
    }

    public function get(): array
    {
        return $this->educations;
    }

    public function getById(): ?string
    {
        if (array_key_exists($this->key . '_years', $this->educations)) {
            return $this->educations[$this->key];
        } else {
            return null;
        }
    }

    public function parce()
    {
        switch ($this->key) {
            case 1:
                return 'higher';
            case 2:
                return 'incomplete_higher';
            case 3:
                return 'secondary';
            case 4:
                return 'incomplete_secondary';

        }
    }
}
