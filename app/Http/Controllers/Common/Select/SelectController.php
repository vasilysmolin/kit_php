<?php

namespace App\Http\Controllers\Common\Select;

use App\Http\Controllers\Controller;
use App\Objects\Education\Constants\Education;
use App\Objects\Reasons\Reasons;
use App\Objects\SalaryType\Constants\SalaryType;
use App\Objects\Schedule\Constants\Schedule;
use App\Objects\States\States;
use App\Objects\Time\Constants\TimeArray;

class SelectController extends Controller
{
    public function experience(): \Illuminate\Http\JsonResponse
    {
        $data = (new TimeArray())->get();
        return response()->json($data);
    }

    public function educations(): \Illuminate\Http\JsonResponse
    {
        $data = (new Education())->get();
        return response()->json($data);
    }

    public function schedules(): \Illuminate\Http\JsonResponse
    {
        $data = (new Schedule())->get();
        return response()->json($data);
    }

    public function salary(): \Illuminate\Http\JsonResponse
    {
        $data = (new SalaryType())->get();
        return response()->json($data);
    }

    public function states(): \Illuminate\Http\JsonResponse
    {
        $data = (new States())->get();
        return response()->json($data);
    }

    public function reasons(): \Illuminate\Http\JsonResponse
    {
        $data = (new Reasons())->get();
        return response()->json($data);
    }
}
