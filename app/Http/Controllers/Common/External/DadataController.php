<?php

namespace App\Http\Controllers\Common\External;

use App\Http\Controllers\Controller;
use App\Objects\Dadata\Dadata;
use Illuminate\Http\Request;

class DadataController extends Controller
{
    public function findCompany(Request $request)
    {
        $dadata = new Dadata();
        $result = $dadata->findCompany($request->inn);
        return response()->json(['has_company' => $dadata->hasCompany($result)]);
    }
}
