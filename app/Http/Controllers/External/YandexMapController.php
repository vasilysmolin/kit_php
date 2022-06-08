<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use YaGeo;

class YandexMapController extends Controller
{

//    public function getAddress(Request $request): \Illuminate\Http\JsonResponse
//    {
//        $data = YaGeo::setQuery($request->address)->load()->getResponse()->getData();
//        return response()->json($data);
//    }

    public function getAddress(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->getAddressByCoords($request->longitude, $request->latitude)
            ->load()
            ->getResponse()
            ->getData();

        return response()->json($data);
    }

    public function getAddressByCoords($longitude, $latitude): array
    {
        $data = YaGeo::setPoint($longitude, $latitude)
            ->load()
            ->getResponse()
            ->getData();

        return $data;
    }

}
