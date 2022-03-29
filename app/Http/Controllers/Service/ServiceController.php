<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryID = $request->category_id;
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }
        $userID = (int) $request->user_id;
        $status = $request->status;
        $states = new States();

        $service = Service::take((int) $take)
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
            ->get();


        $service->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->title = $item->name;
        });

        $count = Service::take((int) $take)
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
//            ->where('active', 1)
            ->count();

        $data = (new JsonHelper())->getIndexStructure(new Service(), $service, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;

        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        unset($formData['category_id']);
        $service = new Service();
        $service->fill($formData);
//        dd($service);
        $service->save();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = ServiceCategory::find($request['category_id']);
            if (isset($category)) {
                $service->category_id = $request['category_id'];
                $service->update();
            }
        }

        $files->save($service, $request['files']);

        return response()->json([], 201, ['Location' => "/services/$service->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $service = Service::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->with('image')
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->first();

        $files = resolve(Files::class);
        if (isset($service->image)) {
            $service->photo = $files->getFilePath($service->image);
//            $service->makeHidden('image');
        }
        $service->title = $service->name;

        abort_unless($service, 404);

        return response()->json($service);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $user = auth('api')->user();

        unset($formData['category_id']);
        $service = Service::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->whereHas('profile.user', function ($q) use ($user) {
                $q->where('id', $user->id);
            })
            ->first();

        $service->fill($formData);
        $service->update();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = ServiceCategory::find($request['category_id']);
            if (isset($category)) {
                $service->category_id = $request['category_id'];
                $service->update();
            }
        }

        $files->save($service, $request['files']);

        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        Service::where('alias', $id)
            ->orWhere('id', (int) $id)->delete();
        return response()->json([], 204);
    }
}
