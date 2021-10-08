<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Import\FoodImport;
use App\Models\RestaurantFood;
use App\Objects\Files;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FoodController extends Controller
{

    public function index(Request $request, $idRes)
    {
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
            ->when($cabinet !== false, function ($q) use ($user) {
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
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
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

    public function food(Request $request)
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
        $files = resolve(Files::class);
        $foods = RestaurantFood::take($take)
            ->skip($skip)
            ->where('active', 1)
            ->when($category, function ($q) use ($category) {
                $q->whereHas('categoryFood', function ($q) use ($category) {
                    $q->where('alias', $category);
                });
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->get();

        $foods->each(function($item) use ($files){
            if(isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }

        });


        $count = RestaurantFood::take($take)
            ->skip($skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->where('active', 1)
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
        $formData['active'] = true;
        $formData['restaurant_id'] = $id;
        $restaurantFood = new RestaurantFood();
        $restaurantFood->fill($formData);
        $restaurantFood->save();

        $files = resolve(Files::class);

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $restaurantFood->image()->create([
                    'mimeType' => $dataFile['mineType'],
                    'extension' => $dataFile['extension'],
                    'name' => $dataFile['name'],
                    'uniqueValue' => $dataFile['name'],
                    'size' => $dataFile['size'],
                ]);

            }
        }

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
            ->when($cabinet !== false, function ($q) use ($user) {
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
        $file = $request->file;
        $user = auth('api')->user();
        Excel::import(new FoodImport(), $file);
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
