<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantFood;
use Illuminate\Http\Request;

class FoodController extends Controller
{

    public function index(Request $request, $alias)
    {
        $restaurant = Restaurant::where('alias',$alias)->firstOrFail();
        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;
        $foods = RestaurantFood::take($take)
            ->skip($skip)
            ->where('active', 1)
            ->where('restaurant_id',$restaurant->getKey())
            ->get();

        return response()->json(['foods' => $foods ]);
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


    public function show($id,$alias)
    {

        $foods = RestaurantFood::
            where('active', 1)
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
