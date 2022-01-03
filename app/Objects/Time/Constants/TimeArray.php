<?php

namespace App\Objects\Time\Constants;

use App\Objects\Time\Contract\TimeInterface;

class TimeArray implements TimeInterface
{
    private $arrTimes = [
        '1_year' => '1 год',
        '2_years' => '2 года',
        '3_years' => '3 года',
        '4_years' => '4 года',
        '5_years' => '5 лет',
        '6_years' => '6 лет',
        '7_years' => '7 лет',
        '8_years' => '8 лет',
        '9_years' => '9 лет',
        '10_years' => '10 лет',
        '11_years' => '11 лет',
        '12_years' => '12 лет',
        '13_years' => '13 лет',
        '14_years' => '14 лет',
        '15_years' => '15 лет',
        '16_years' => '16 лет',
        '17_years' => '17 лет',
        '18_years' => '18 лет',
        '19_years' => '19 лет',
        '20_years' => '20 лет',
        'more_20_years' => 'более 20 лет',
    ];

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->key = $key ;
        $this->value = $value;
    }

    public function arrTimes(): array
    {
        return $this->arrTimes;
    }

    public function getTime(): ?string
    {
        if (array_key_exists($this->key . '_years', $this->arrTimes)) {
            return $this->arrTimes[$this->key];
        } else {
            return null;
        }
    }

    public function parce()
    {
        if (array_key_exists($this->key . '_years', $this->arrTimes)) {
            return $this->key . '_years';
        } else {
            return null;
        }
    }
}
