<?php

namespace App\Http\Controllers\Common\Select;

use App\Http\Controllers\Controller;
use App\Objects\Time\Constants\TimeArray;


class SelectController extends Controller
{
    public function experience(): \Illuminate\Http\JsonResponse
    {
        $data = (new TimeArray())->arrTimes();
        return response()->json($data);
    }

}
