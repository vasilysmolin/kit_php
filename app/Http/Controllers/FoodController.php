<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Import\FoodImport;
use App\Models\Restaurant;
use App\Models\RestaurantFood;
use Beauty\Jobs\ClientsImportJob;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FoodController extends Controller
{

    public function index(Request $request, $idRes)
    {
//        $restaurant = Restaurant::where('alias',$alias)->firstOrFail();
        $cabinet = false;
        $user = auth('api')->user();
        if(isset($user) && $request->from == 'cabinet') {
            $cabinet = true;
        }

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
            ->when(isset($cabinet), function ($q) use ($user) {
                $q->whereHas('restaurant.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
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


    public function store(Request $request, $id)
    {
        $formData = $request->all();
        $formData['active'] = 1;
        $formData['restaurant_id'] = $id;
        $restaurant = new RestaurantFood();
        $restaurant->fill($formData);
        $restaurant->save();

        return response()->json([], 201);
    }


    public function show(Request $request, $id)
    {

        $cabinet = false;
        $user = auth('api')->user();
        if(isset($user) && $request->from == 'cabinet') {
            $cabinet = true;
        }

        $foods = RestaurantFood::
            where('active', 1)
            ->when(isset($cabinet), function ($q) use ($user) {
                $q->whereHas('restaurant.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->with(['categoryFood','restaurant.user'])
            ->where('id', $id)
            ->first();

        return response()->json($foods);
    }

    public function import(Request $request)
    {
        $dishes = $request->dishes;
        $user = auth('api')->user();

        Excel::import(new FoodImport(), $dishes);
        return response()->json([], 204);
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


    public function update(Request $request, $id)
    {
        $formData = json_decode($request->getContent(), true);
        $formData['active'] = 1;
        $restaurant = RestaurantFood::find($id);
        $restaurant->fill($formData);
        $restaurant->update();

        return response()->json([], 204);
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
