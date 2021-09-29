<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {


        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;

        $restaurants = Restaurant::take($take)
            ->skip($skip)
            ->where('active', 1)
            ->get();

        $count = Restaurant::take($take)
            ->skip($skip)

            ->where('active', 1)
            ->count();

        $data = [
            'meta' => [
                'skip' => $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0
            ],
            'restaurants' => $restaurants
        ];

        return response()->json($data);
    }


    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([],200);
    }


    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $formData['user_id'] = auth('api')->user()->getAuthIdentifier();
        $restaurant = new Restaurant();
        $restaurant->fill($formData);
        $restaurant->save();

        return response()->json([],201);
    }


    public function show($alias): \Illuminate\Http\JsonResponse
    {
        $restaurant = Restaurant::where('alias', $alias)
            ->with('uploads')
            ->first();

        return response()->json($restaurant);
    }


    public function edit($id): \Illuminate\Http\JsonResponse
    {

        return response()->json([],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        Restaurant::destroy($id);
        return response()->json([],204);
    }
}
