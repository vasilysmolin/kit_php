<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use YaGeo;

class YandexMapController extends Controller
{

    public function getAddress(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = YaGeo::setQuery($request->address)->load()->getResponse()->getData();
        return response()->json($data);
    }

}
