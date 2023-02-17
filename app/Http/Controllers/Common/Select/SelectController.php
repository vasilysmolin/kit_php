<?php

namespace App\Http\Controllers\Common\Select;

use App\Http\Controllers\Controller;
use App\Objects\Education\Constants\Education;
use App\Objects\Reasons\Reasons;
use App\Objects\SalaryType\Constants\SalaryType;
use App\Objects\Schedule\Constants\Schedule;
use App\Objects\States\States;
use App\Objects\Time\Constants\TimeArray;
use App\Objects\TypeHouse\DeadLine;
use App\Objects\TypeHouse\Elite;
use App\Objects\TypeHouse\Finishing;
use App\Objects\TypeHouse\TypeHouse;
use App\Objects\TypeService\TypeService;

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

    public function typeServices(): \Illuminate\Http\JsonResponse
    {
        $data = (new TypeService())->get();
        return response()->json($data);
    }

    public function typeHouse(): \Illuminate\Http\JsonResponse
    {
        $data = TypeHouse::all();
        return response()->json($data);
    }

    public function finishing(): \Illuminate\Http\JsonResponse
    {
        $data = Finishing::all();
        return response()->json($data);
    }

    public function deadLine(): \Illuminate\Http\JsonResponse
    {
        $data = DeadLine::all();
        return response()->json($data);
    }

    public function elite(): \Illuminate\Http\JsonResponse
    {
        $data = Elite::all();
        return response()->json($data);
    }
}
