<?php

namespace App\Objects\Dadata;

use MoveMoveIo\DaData\Enums\BranchType;
use MoveMoveIo\DaData\Enums\CompanyType;
use MoveMoveIo\DaData\Facades\DaDataCompany;

class Dadata
{

    public function findCompany(string $string): \MoveMoveIo\DaData\DaDataCompany
    {
        return DaDataCompany::id($string, 1, null, BranchType::MAIN, CompanyType::LEGAL);
    }

    public function hasCompany($company): bool
    {
        return count($company['suggestions']) > 0;
    }
}
