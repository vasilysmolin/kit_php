<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantFood;
use Illuminate\Http\Request;

class FoodController extends Controller
{

    public function index(Request $request, $id)
    {
//        $restaurant = Restaurant::where('alias',$alias)->firstOrFail();
        $take = (int) $request->take ?? 25;
        $skip = (int) $request->skip ?? 0;
        $category = $request->category;
        $foods = RestaurantFood::take($take)
            ->skip($skip)
            ->where('active', 1)
            ->when(isset($category), function ($q) use ($category) {
                $q->whereHas('categoryFood', function ($q) use ($category) {
                    $q->where('alias', $category);
                });
            })
            ->where('restaurant_id', $id)
            ->get();


        $count = RestaurantFood::take($take)
            ->skip($skip)
            ->where('active', 1)
            ->where('restaurant_id', $id)
            ->count();

        $data = [
            'meta' => [
                'skip' => $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0,
            ],
            'restaurants' => $foods,
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }


    public function show($alias)
    {

        $foods = RestaurantFood::
            where('active', 1)
            ->with(['categoryFood','restaurant'])
            ->where('alias', $alias)
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
