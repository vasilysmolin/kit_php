<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Realty\RealtyShowRequest;
use App\Http\Requests\Seller\SellerHouseStoreRequest;
use App\Http\Requests\Seller\SellerHouseUpdateRequest;
use App\Models\House;
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
        $files->save($house, $request['files']);

        return response()->json([], 201, ['Location' => "/house/$house->id"]);
    }

    public function show(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $cabinet = isset($user) && $request->from === 'cabinet';
        $account = $request->get('accounts');
        $house = SellerHouse::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->when($cabinet !== false, function ($q) use ($account) {
                $q->whereHas('profile', function ($q) use ($account) {
                    $q->where('id', $account['profile_id']);
                });
            })
            ->first();

        abort_unless($house, 404);

        $files = resolve(Files::class);
        if ($house->images->count() > 0) {
            $house->photo = $files->getFilePath($house->latestImage);
            $house->photos = collect([]);
            $house->images->each(function ($image) use ($files, $house) {
                $house->photos->push($files->getFilePath($image));
            });
            $house->photos = array_filter($house->photos->toArray());
        }
        $house->makeHidden('image');
        $house->makeHidden('latestImage');
        $house->makeHidden('images');

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
        $files->save($house, $request['files']);

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
