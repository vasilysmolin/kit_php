<?php

namespace App\Http\Controllers\Food;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Import\FoodImport;
use App\Http\Requests\StoreRestaurantFoodRequest;
use App\Http\Requests\UpdateRestaurantFoodRequest;
use App\Models\FoodRestaurant;
use App\Models\FoodRestaurantDishes;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class DishesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','show','foods']]);
    }

    public function index(Request $request, $idRes)
    {

        $cabinet = false;
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        }

        $files = resolve(Files::class);
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;

        $id = isset($request->id) ? explode(',', $request->id) : null;
        $category = $request->category;
        $foods = FoodRestaurantDishes::take((int) $take)
            ->skip((int) $skip)
            ->where('active', 1)
            ->when(isset($category), function ($q) use ($category) {
                $q->whereHas('categoryFood', function ($q) use ($category) {
                    $q->where('alias', $category);
                });
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('restaurant_id', $idRes)
            ->get();

        $foods->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->categoryDishesID = $item->categories->pluck('id');
            $item->makeHidden('categories');
        });


        $count = FoodRestaurantDishes::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->where('active', 1)
            ->where('restaurant_id', $idRes)
            ->count();

        $data = (new JsonHelper())->getIndexStructure(new FoodRestaurantDishes(), $foods, $count, (int) $skip);

        return response()->json($data);
    }

    public function foods(Request $request)
    {
//        $restaurant = Restaurant::where('alias',$alias)->firstOrFail();
        $cabinet = false;
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        }

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $category = $request->category;
        $files = resolve(Files::class);
        $foods = FoodRestaurantDishes::take((int) $take)
            ->skip((int) $skip)
            ->where('active', 1)
            ->when($category, function ($q) use ($category) {
                $q->whereHas('categoryFood', function ($q) use ($category) {
                    $q->where('alias', $category);
                });
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->get();

        $foods->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->categoryDishesID = $item->categories->pluck('id');
            $item->makeHidden('categories');
        });


        $count = FoodRestaurantDishes::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->where('active', 1)
            ->count();

        $data = (new JsonHelper())->getIndexStructure(new FoodRestaurantDishes(), $foods, $count, (int) $skip);

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

    public function store(StoreRestaurantFoodRequest $request, $id)
    {
        $formData = $request->all();
        $formData['active'] = 1;
        $formData['restaurant_id'] = (int) $id;
        $dishes = new FoodRestaurantDishes();
        unset($formData['categoryDishesID']);
        if (isset($formData['name'])) {
            $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        }
        $dishes->fill($formData);
        $dishes->save();

        if (isset($request['categoryDishesID'])) {
            $dishes->categories()->sync($request['categoryDishesID']);
        }

        $files = resolve(Files::class);

        $files->save($dishes, $request['files']);

        return response()->json([], 201, ['Location' => "/dishes/$dishes->id"]);
    }

    public function show(Request $request, $id)
    {

        $cabinet = false;
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        }

        $food = FoodRestaurantDishes::
            where('active', 1)
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('restaurant.profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->with(['categories','restaurant'])
            ->where('alias', $id)
            ->orWhere('id', (int) $id)
            ->first();

        $files = resolve(Files::class);
        if (isset($food->image)) {
            $food->photo = $files->getFilePath($food->image);
            $food->makeHidden('image');
        }

        abort_unless($food, 404);
        $food->categoryDishesID = $food->categories->pluck('id');
        $food->restaurant_id = $food->restaurant->id;
        $food->makeHidden('categories');
        $food->makeHidden('restaurant');
        return response()->json($food);
    }

    public function import(Request $request)
    {
        $file = $request->file;
//        $user = auth('api')->user();
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

    public function update(UpdateRestaurantFoodRequest $request, $id)
    {
        $formData = $request->all();
        $user = auth('api')->user();
        $formData['active'] = 1;
        if (isset($formData['name'])) {
            $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        }

        $dishes = FoodRestaurantDishes::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->whereHas('restaurant.profile.user', function ($q) use ($user) {
                $q->where('id', $user->id);
            })->first();

        if (!isset($dishes)) {
            throw new ModelNotFoundException("__('validation.permissionDenied')", Response::HTTP_FORBIDDEN);
        }
        $dishes->fill($formData);
        $dishes->update();

        if (isset($request['categoryDishesID'])) {
            $dishes->categories()->sync($request['categoryDishesID']);
        }

        $files = resolve(Files::class);

        $files->save($dishes, $request['files']);

        return response()->json([], 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        FoodRestaurantDishes::where('alias', $id)
            ->orWhere('id', (int) $id)->delete();
        return response()->json([], 204);
    }
}
