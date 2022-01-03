<?php

namespace App\Http\Controllers\Food;

use App\Http\Controllers\Controller;
use App\Models\FoodCategoryRestaurant;
use App\Objects\JsonHelper;
use Illuminate\Http\Request;

class CategoryRestaurantController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;
        $ids = $request->id ?? null;

        $restaurants = FoodCategoryRestaurant::take((int)$take)
            ->skip((int)$skip)
            ->when(isset($ids), function ($q) use ($ids) {
                $q->whereIn('id', $ids);
            })
            ->where('active', 1)
            ->get();

        $count = FoodCategoryRestaurant::take((int)$take)
            ->skip((int)$skip)
            ->when(isset($ids), function ($q) use ($ids) {
                $q->whereIn('id', $ids);
            })
            ->where('active', 1)
            ->count();

        $data = (new JsonHelper())->getIndexStructure(new FoodCategoryRestaurant, $restaurants, $count, (int) $skip);

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
