<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Models\Restaurant;
use App\Objects\Files;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $restaurants = Restaurant::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->with('image')
            ->where('active', 1)
            ->get();

        $restaurants->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
        });

        $count = Restaurant::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
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
        $restaurant = new Restaurant();
        $restaurant->fill($formData);
        $restaurant->save();
        $files = resolve(Files::class);

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

        return response()->json([], 201);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $restaurant = Restaurant::where('id', $id)
//            ->with('uploads')
            ->first();

        abort_unless($restaurant, 404);

        return response()->json($restaurant);
    }

    public function update(StoreRestaurantRequest $request, $id)
    {
        $formData = json_decode($request->getContent(), true);
        $formData['user_id'] = auth('api')->user()->getAuthIdentifier();
        $restaurant = Restaurant::find($id);
        $restaurant->fill($formData);
        $restaurant->update();

        return response()->json([], 204);
    }

    public function destroy($id)
    {
        Restaurant::destroy($id);
        return response()->json([], 204);
    }
}
