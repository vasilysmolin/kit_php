<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = (int) $request->take ?? 25;
        $skip = (int) $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;

        $restaurants = Restaurant::take($take)
            ->skip($skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('active', 1)
            ->get();

        $count = Restaurant::take($take)
            ->skip($skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('active', 1)
            ->count();

        $data = [
            'meta' => [
                'skip' => $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0,
            ],
            'restaurants' => $restaurants,
        ];

        return response()->json($data);
    }


    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([], 200);
    }


    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $formData['user_id'] = auth('api')->user()->getAuthIdentifier();
        $restaurant = new Restaurant();
        $restaurant->fill($formData);
        $restaurant->save();

        return response()->json([], 201);
    }


    public function show($id): \Illuminate\Http\JsonResponse
    {
        $restaurant = Restaurant::where('alias', $id)
//            ->with('uploads')
            ->first();

        return response()->json($restaurant);
    }


    public function edit($id): \Illuminate\Http\JsonResponse
    {
        $restaurant = Restaurant::where('alias', $id)
//            ->with('uploads')
            ->first();
        return response()->json($restaurant);
    }


    public function update(Request $request, $id)
    {
        $formData = $request->all();
        $formData['user_id'] = auth('api')->user()->getAuthIdentifier();
        $formData['restaurant_id'] = $id;
        $restaurant = new Restaurant();
        $restaurant->fill($formData);
        $restaurant->save();

        return response()->json([], 204);
    }


    public function destroy($id)
    {
        Restaurant::destroy($id);
        return response()->json([], 204);
    }
}
