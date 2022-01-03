<?php

namespace App\Http\Controllers\Food;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryFoodRequest;
use App\Models\FoodDishesCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Http\Request;

class CategoryFoodController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $take = $request->take ?? config('settings.take_twenty_five');
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

        $data = (new JsonHelper())->getIndexStructure(new FoodDishesCategory, $restaurants, $count, (int) $skip);

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

        $files->save($restaurantFood, $request['files']);

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
        $restaurantFood = FoodDishesCategory::find($id);
        $restaurantFood->fill($formData);
        $restaurantFood->update();

        $files = resolve(Files::class);
        $files->save($restaurantFood, $request['files']);

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
