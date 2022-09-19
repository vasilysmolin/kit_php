<?php

namespace App\Objects\Dadata;

use Illuminate\Http\Client\ConnectionException;
use MoveMoveIo\DaData\Enums\BranchType;
use MoveMoveIo\DaData\Enums\CompanyType;
use MoveMoveIo\DaData\Enums\Language;
use MoveMoveIo\DaData\Facades\DaDataAddress;
use MoveMoveIo\DaData\Facades\DaDataCompany;

class Dadata
{
    public function findCompany(string $string)
    {
        try {
            $dadata = DaDataCompany::id($string, 1, null, BranchType::MAIN, CompanyType::LEGAL);
        } catch (\Exception | ConnectionException $e) {
            dd($e->getMessage());
        }
        return $dadata;
    }

    public function findAddress(string $string)
    {
        try {
            $dadata = DaDataAddress::prompt($string, 10, Language::RU, [], [], [], ["value" => "house"], ["value" => "house"]);
        } catch (\Exception | ConnectionException $e) {
            dd($e->getMessage());
        }
        return $dadata;
    }

    public function hasCompany($company): bool
    {
        return count($company['suggestions']) > 0;
    }

    public function getData($data): array
    {
        return $data['suggestions'];
    }

    public function getCompanyName($company): ?string
    {
        $company = collect($company['suggestions'])->first();
        return !empty($company) ? $company['value'] : 'Не найдена';
    }
}
