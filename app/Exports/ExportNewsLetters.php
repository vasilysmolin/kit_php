<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExportNewsLetters implements FromCollection
{
    private $letters;

    public function __construct($collection)
    {
        $this->letters = $collection;
    }

    public function collection()
    {
        return $this->letters;
    }
}
