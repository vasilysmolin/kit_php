<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExportUsers implements FromCollection
{
    private $users;

    public function __construct($collection)
    {
        $this->users = $collection;
    }

    public function collection()
    {
        return $this->users;
    }
}
