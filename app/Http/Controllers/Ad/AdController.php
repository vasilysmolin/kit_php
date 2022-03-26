<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\CatalogAd;
use App\Models\CatalogAdCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AdController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryID = $request->category_id;
        $userID = (int) $request->user_id;
        $status = $request->status;
        $states = new States();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $catalogAd = CatalogAd::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when(!empty($status) && $states->isExists($status), function ($q) use ($status) {
                $q->where('state', $status);
            })
            ->when(!empty($userID), function ($q) use ($userID) {
                $q->whereHas('profile.user', function ($q) use ($userID) {
                    $q->where('id', $userID);
                });
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->orderBy('id', 'DESC')
            ->with('image', 'categories')
//            ->where('active', 1)
            ->get();


        $catalogAd->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->title = $item->name;
                $item->makeHidden('image');
            }
        });

        $count = CatalogAd::when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
        })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when(!empty($status) && $states->isExists($status), function ($q) use ($status) {
                $q->where('state', $status);
            })
            ->when(!empty($userID), function ($q) use ($userID) {
                $q->whereHas('profile.user', function ($q) use ($userID) {
                    $q->where('id', $userID);
                });
            })
//            ->where('active', 1)
            ->skip((int) $skip)->take((int) $take)
            ->count();

        $data = (new JsonHelper())->getIndexStructure(new CatalogAd(), $catalogAd, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;

        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        unset($formData['category_id']);
        $catalogAd = new CatalogAd();
        $catalogAd->fill($formData);
        $catalogAd->save();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = CatalogAdCategory::find($request['category_id']);
            if (isset($category)) {
                $catalogAd->category_id = $request['category_id'];
                $catalogAd->update();
            }
        }

        $files->save($catalogAd, $request['files']);

        return response()->json([], 201, ['Location' => "/ads/$catalogAd->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $catalogAd = CatalogAd::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->with('image')
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->first();

        $files = resolve(Files::class);
        if (isset($catalogAd->image)) {
            $catalogAd->photo = $files->getFilePath($catalogAd->image);
            $catalogAd->title = $catalogAd->name;
//            $catalogAd->makeHidden('image');
        }

        abort_unless($catalogAd, 404);

        return response()->json($catalogAd);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $user = auth('api')->user();

        unset($formData['category_id']);
        $catalogAd = CatalogAd::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->whereHas('profile.user', function ($q) use ($user) {
                $q->where('id', $user->id);
            })
            ->first();

        $catalogAd->fill($formData);

        $catalogAd->update();

        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = CatalogAdCategory::find($request['category_id']);
            if (isset($category)) {
                $catalogAd->category_id = $request['category_id'];
                $catalogAd->update();
            }
        }

        $files->save($catalogAd, $request['files']);

        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        CatalogAd::where('alias', $id)
            ->orWhere('id', (int) $id)->delete();
        return response()->json([], 204);
    }
}
