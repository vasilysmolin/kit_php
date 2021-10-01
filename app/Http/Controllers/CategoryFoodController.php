<?php

namespace App\Http\Controllers;

use App\Models\CategoryFood;
use App\Models\RestaurantFood;
use Illuminate\Http\Request;

class CategoryFoodController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $take = (int) $request->take ?? 25;
        $skip = (int) $request->skip ?? 0;
        $restaurants = CategoryFood::take($take)
            ->skip($skip)
            ->where('active', 1)
            ->get();

        $count = CategoryFood::take($take)
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    public function show($alias): \Illuminate\Http\JsonResponse
    {
        $foods = CategoryFood::where('active', 1)
            ->where('alias',$alias)
            ->first();

        return response()->json($foods);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
