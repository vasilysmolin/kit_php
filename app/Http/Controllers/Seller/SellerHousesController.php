<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Seller\SellerHouseStoreRequest;
use App\Http\Requests\Seller\SellerHouseUpdateRequest;
use App\Models\SellerHouse;
use App\Objects\Files;
use Illuminate\Http\Request;

class SellerHousesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($request);
    }

    public function store(SellerHouseStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $account = $request->get('accounts');
        $formData['profile_id'] = $account['profile_id'];

        $house = new SellerHouse();
        $house->fill($formData);
        $house->save();
        $files = resolve(Files::class);
        $files->save($house, [$request['label']], 'label');
        $files->save($house, [$request['background']], 'background');

        return response()->json([], 201, ['Location' => "/house/$house->id"]);
    }

    public function show(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $cabinet = isset($user) && $request->from === 'cabinet';
        $account = $request->get('accounts');
        $house = SellerHouse::with('profile.user')->has('profile.user')
            ->when($cabinet !== false, function ($q) use ($account) {
                $q->whereHas('profile', function ($q) use ($account) {
                    $q->where('id', $account['profile_id']);
                });
            })
            ->first();
        if (empty($house)) {
            return response()->json([
                'description' => null,
                'logo' => null,
                'background_image' => null,
            ]);
        }

        $files = resolve(Files::class);
        $house->logo = !empty($house->label) ? $files->getFilePath($house->label) : null;
        $house->background_image = !empty($house->background) ? $files->getFilePath($house->background) : null;
        $house->makeHidden('label');
        $house->makeHidden('background');

        return response()->json($house);
    }

    public function update(SellerHouseUpdateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $house = SellerHouse::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $house->fill($formData);
        $house->update();
        $files = resolve(Files::class);
        $files->save($house, [$request['label']], 'label');
        $files->save($house, [$request['background']], 'background');

        return response()->json([], 204);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $house = SellerHouse::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (isset($house)) {
            $house->delete();
        }
        return response()->json([], 204);
    }
}
