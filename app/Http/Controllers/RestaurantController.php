<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Models\Restaurant;
use App\Objects\Files;
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

        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }
        $restaurants = Restaurant::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->with('image','categoriesRestaurant')
            ->where('active', 1)
            ->get();


        $restaurants->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->category_restaurant_id = $item->categoriesRestaurant->pluck('id');
            $item->makeHidden('categoriesRestaurant');
        });

        $count = Restaurant::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->where('active', 1)
            ->count();

        $data = [
            'meta' => [
                'skip' => (int) $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0,
            ],
            'restaurants' => $restaurants,
        ];

        return response()->json($data);
    }

    public function store(StoreRestaurantRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $formData['user_id'] = auth('api')->user()->getAuthIdentifier();
        $formData['active'] = true;
        unset($formData['address']);
        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        unset($formData['category_restaurant_id']);
        $restaurant = new Restaurant();
        $restaurant->fill($formData);
        $restaurant->save();
        $files = resolve(Files::class);

        if (isset($request['category_restaurant_id'])) {
           $restaurant->categoriesRestaurant()->sync($request['category_restaurant_id']);
        }

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $restaurant->image()->create([
                    'mimeType' => $dataFile['mineType'],
                    'extension' => $dataFile['extension'],
                    'name' => $dataFile['name'],
                    'uniqueValue' => $dataFile['name'],
                    'size' => $dataFile['size'],
                ]);
            }
        }

        return response()->json([], 201, ['Location' => "/restaurants/$restaurant->id"]);
    }

    public function show(Request $request,$id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $restaurant = Restaurant::where('id', $id)
            ->with('image','categoriesRestaurant')
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->first();

        $files = resolve(Files::class);
        if (isset($restaurant->image)) {
            $restaurant->photo = $files->getFilePath($restaurant->image);
            $restaurant->makeHidden('image');
        }

        abort_unless($restaurant, 404);
        $restaurant->category_restaurant_id = $restaurant->categoriesRestaurant->pluck('id');
        $restaurant->makeHidden('categoriesRestaurant');

        return response()->json($restaurant);
    }

    public function update(UpdateRestaurantRequest $request, $id)
    {
        $formData = json_decode($request->getContent(), true);
        $user = auth('api')->user();
        $formData['user_id'] = auth('api')->user()->getAuthIdentifier();
        if (isset($formData['name'])) {
            $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        }
        unset($formData['address']);
        unset($formData['category_restaurant_id']);
        $restaurant = Restaurant::where('id', $id)
            ->whereHas('user', function ($q) use ($user) {
                $q->where('id', $user->id);
            })->first();

        if(!isset($restaurant)) {
            throw new ModelNotFoundException("Доступ запрещен", Response::HTTP_FORBIDDEN);
        }

        $restaurant->fill($formData);
        $restaurant->update();

        if (isset($request['category_restaurant_id'])) {
            $restaurant->categoriesRestaurant()->sync($request['category_restaurant_id']);
        }

        return response()->json([], 204);
    }

    public function destroy($id)
    {
        Restaurant::destroy($id);
        return response()->json([], 204);
    }
}
