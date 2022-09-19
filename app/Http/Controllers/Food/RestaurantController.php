<?php

namespace App\Http\Controllers\Food;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Models\FoodRestaurant;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','show']]);
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryRestaurantID = $request->categoryRestaurantID;
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $states = new States();

        $restaurants = FoodRestaurant::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when(isset($categoryRestaurantID), function ($q) use ($categoryRestaurantID) {
                $q->whereHas('categories', function ($q) use ($categoryRestaurantID) {
                    $q->whereIn('category_id', $categoryRestaurantID);
                });
            })
            ->when($cabinet === true, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when($catalog === true, function ($q) use ($states) {
                $q ->whereHas('profile.user', function ($q) use ($states) {
                    $q->where('state', $states->active());
                });
            })
            ->with('image', 'categories')
            ->where('active', 1)
            ->get();


        $restaurants->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->categoryRestaurantID = $item->categories->pluck('id');
            $item->restaurantFoodID = $item->dishes->pluck('id');
            $item->makeHidden('categories');
        });

        $count = FoodRestaurant::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when(isset($categoryRestaurantID), function ($q) use ($categoryRestaurantID) {
                $q->whereHas('categories', function ($q) use ($categoryRestaurantID) {
                    $q->whereIn('category_id', $categoryRestaurantID);
                });
            })
            ->when($cabinet === true, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when($catalog === true, function ($q) use ($states) {
                $q ->whereHas('profile.user', function ($q) use ($states) {
                    $q->where('state', $states->active());
                });
            })
            ->where('active', 1)
            ->count();
        $data = (new JsonHelper())->getIndexStructure(new FoodRestaurant(), $restaurants, $count, (int) $skip);
        return response()->json($data);
    }

    public function store(StoreRestaurantRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;

        if (isset($formData['address']) && isset($formData['address']['coords']) && is_array($formData['address']['coords'])) {
            $formData['latitude'] = $formData['address']['coords'][0] ?? 0;
            $formData['longitude'] = $formData['address']['coords'][1] ?? 0;
        }
        if (isset($formData['address']) && isset($formData['address']['text'])) {
            $formData['address'] = $formData['address']['text'];
        } else {
            $formData['address'] = '';
        }

        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        unset($formData['categoryRestaurantID']);
        $restaurant = new FoodRestaurant();
        $restaurant->fill($formData);

        $restaurant->save();
        $files = resolve(Files::class);

        if (isset($request['categoryRestaurantID'])) {
            $restaurant->categories()->sync($request['categoryRestaurantID']);
        }
        $files->save($restaurant, $request['files']);

        return response()->json([], 201, ['Location' => "/restaurants/$restaurant->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $states = new States();

        $restaurant = FoodRestaurant::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->with('image', 'categories:id', 'dishes:id', 'dishes')
            ->when($cabinet === true, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when($catalog === true, function ($q) use ($states) {
                $q ->whereHas('profile.user', function ($q) use ($states) {
                    $q->where('state', $states->active());
                });
            })
            ->first();

        $files = resolve(Files::class);
        if (isset($restaurant->image)) {
            $restaurant->photo = $files->getFilePath($restaurant->image);
//            $restaurant->makeHidden('image');
        }

        abort_unless($restaurant, 404);
        $restaurant->categoryRestaurantID = $restaurant->categories->pluck('id');
        $restaurant->restaurantFoodID = $restaurant->dishes->pluck('id');
        $restaurant->makeHidden('categories');
//        $restaurant->makeHidden('dishes');

        return response()->json($restaurant);
    }

    public function update(UpdateRestaurantRequest $request, $id)
    {
        $formData = $request->all();
        $user = auth('api')->user();
        $formData['profile_id'] = auth('api')->user()->profile->id;

        if (isset($formData['name'])) {
            $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        }

        if (isset($formData['address']) && isset($formData['address']['coords']) && is_array($formData['address']['coords'])) {
            $formData['latitude'] = $formData['address']['coords'][0] ?? 0;
            $formData['longitude'] = $formData['address']['coords'][1] ?? 0;
        }

        if (isset($formData['address']) && isset($formData['address']['text'])) {
            $formData['address'] = $formData['address']['text'];
        }
        unset($formData['categoryRestaurantID']);
        $restaurant = FoodRestaurant::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->whereHas('profile.user', function ($q) use ($user) {
                $q->where('id', $user->id);
            })->first();

        if (!isset($restaurant)) {
            throw new ModelNotFoundException("__('validation.permissionDenied')", Response::HTTP_FORBIDDEN);
        }

        $restaurant->fill($formData);

        $restaurant->update();

        $files = resolve(Files::class);

        if (isset($request['categoryRestaurantID'])) {
            $restaurant->categories()->sync($request['categoryRestaurantID']);
        }

        $files->save($restaurant, $request['files']);

        return response()->json([], 204);
    }

    public function destroy($id)
    {
        FoodRestaurant::where('alias', $id)
            ->orWhere('id', (int) $id)->delete();
        return response()->json([], 204);
    }
}
