<?php

namespace App\Objects\Dadata;

use Illuminate\Http\Client\ConnectionException;
use MoveMoveIo\DaData\Enums\BranchType;
use MoveMoveIo\DaData\Enums\CompanyType;
use MoveMoveIo\DaData\Facades\DaDataCompany;

class Dadata
{

    public function findCompany(string $string): \MoveMoveIo\DaData\DaDataCompany
    {
        try {
            $dadata = DaDataCompany::id($string, 1, null, BranchType::MAIN, CompanyType::LEGAL);
        } catch (\Exception | ConnectionException $e) {
            dd($e->getMessage());
        }
        return $dadata;
    }

    public function hasCompany($company): bool
    {
        return count($company['suggestions']) > 0;
    }
}
