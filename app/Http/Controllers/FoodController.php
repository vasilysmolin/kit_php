<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantFood;
use Illuminate\Http\Request;

class FoodController extends Controller
{

    public function index(Request $request, $idRes)
    {
//        $restaurant = Restaurant::where('alias',$alias)->firstOrFail();
        $take = (int) $request->take ?? 25;
        $skip = (int) $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $category = $request->category;
        $foods = RestaurantFood::take($take)
            ->skip($skip)
            ->where('active', 1)
            ->when(isset($category), function ($q) use ($category) {
                $q->whereHas('categoryFood', function ($q) use ($category) {
                    $q->where('alias', $category);
                });
            })
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('restaurant_id', $idRes)
            ->get();


        $count = RestaurantFood::take($take)
            ->skip($skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('active', 1)
            ->where('restaurant_id', $idRes)
            ->count();

        $data = [
            'meta' => [
                'skip' => $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0,
            ],
            'dishes' => $foods,
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
