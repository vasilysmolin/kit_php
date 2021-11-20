<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryFoodRequest;
use App\Models\FoodDishesCategory;
use App\Models\FoodRestaurantDishes;
use App\Objects\Files;
use Illuminate\Http\Request;

class CategoryFoodController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;
        $ids = $request->id ?? null;
        $restaurants = FoodDishesCategory::take((int)$take)
            ->skip((int)$skip)
            ->when(isset($ids), function ($q) use ($ids) {
                $q->whereIn('id', $ids);
            })
            ->where('active', 1)
            ->get();

        $count = FoodDishesCategory::take((int)$take)
            ->skip((int)$skip)
            ->when(isset($ids), function ($q) use ($ids) {
                $q->whereIn('id', $ids);
            })
            ->where('active', 1)
            ->count();

        $data = [
            'meta' => [
                'skip' => (int) $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0,
            ],
            'categories' => $restaurants,
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
     * @param StoreCategoryFoodRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryFoodRequest $request)
    {
        $formData = $request->all();
        $formData['active'] = true;
        $restaurantFood = new FoodDishesCategory();
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


    public function show($alias): \Illuminate\Http\JsonResponse
    {
        $categoryFoods = FoodDishesCategory::where('active', 1)
            ->where('alias', $alias)
            ->first();

        abort_unless($categoryFoods, 404);

        return response()->json($categoryFoods);
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


    public function update(StoreCategoryFoodRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = json_decode($request->getContent(), true);
        $formData['active'] = 1;
        $restaurant = FoodDishesCategory::find($id);
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
