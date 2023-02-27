<?php

namespace App\Http\Controllers\Realty;

use App\Events\SaveLogsEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\RealtyMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Ad\AdStateRequest;
use App\Http\Requests\Realty\HouseStoreRequest;
use App\Http\Requests\Realty\HouseUpdateRequest;
use App\Http\Requests\Realty\RealtyIndexRequest;
use App\Http\Requests\Realty\RealtyShowRequest;
use App\Models\House;
use App\Models\RealtyCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;

class HouseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
        $this->middleware(['auth:api', RealtyMiddleware::class])
            ->only('destroy', 'update', 'restore', 'state', 'sort');
    }

    public function index(RealtyIndexRequest $request): \Illuminate\Http\JsonResponse
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $userID = (int) $request->user_id;
        $state = $request->state;
        $name = $request->name;
        $alias = $request->alias;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $querySearch = $request->querySearch;
        $account = $request->get('accounts');
        $realtyIds = [];

        $builder = House::when(!empty($id) && is_array($id), function ($query) use ($id) {
            $query->whereIn('id', $id);
        })
            ->when(!empty($realtyIds), function ($query) use ($realtyIds) {
                $query->whereIn('id', $realtyIds);
            })
            ->when(!empty($alias), function ($query) use ($alias) {
                $category = RealtyCategory::where('alias', $alias)
                    ->with('categories')
                    ->first();
                if (!empty($category)) {
                    $categoriesID = $this->iter($category, []);
                    $query->whereHas('categories', function ($q) use ($categoriesID) {
                        $q->whereIn('id', $categoriesID);
                    });
                }
            })
            ->when(!empty($state) && $states->isExists($state), function ($q) use ($state) {
                $q->where('state', $state);
            })
            ->when(!empty($name), function ($q) use ($name) {
                $q->where('name', 'ilike', "%{$name}%");
            })
            ->when(!empty($userID), function ($q) use ($userID) {
                $q->whereHas('profile.user', function ($q) use ($userID) {
                    $q->where('id', $userID);
                });
            })
            ->when($cabinet === true, function ($q) use ($account) {
                $q->whereHas('profile', function ($q) use ($account) {
                    $q->where('id', $account['profile_id']);
                });
            })
            ->when($catalog === true, function ($q) use ($states) {
                $q ->whereHas('profile.user', function ($q) use ($states) {
                    $q->where('state', $states->active());
                });
            })
            ->orderBy('sort', 'ASC');

        $builderCount = clone $builder;
        $house = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('city:id,region_id,name', 'city.region:id,full_name', 'image', 'agent', 'profile.user', 'profile.person')
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();
        $count = $builderCount->count();

        $house->each(function ($item) use ($files) {
            $street = str_replace('.', '', $item->street);
            $item->full_address = rtrim(collect([$item->city->region->full_name, $item->city->name, $street, $item->house])->join(', '), ", ");
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->latestImage);
                $item->makeHidden('image');
            }
                $item->title = $item->name;
        });
        $data = (new JsonHelper())->getIndexStructure(new House(), $house, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(HouseStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $account = $request->get('accounts');
        $formData['profile_id'] = $account['profile_id'];

        $house = new House();
        $house->fill($formData);
        $house->save();

        if (isset($request['name_agent'])) {
            $agent = $house->agent;
            if (!empty($agent)) {
                $house->name = $request['name_agent'];
                $house->update();
            } else {
                $house->agent()->create([
                    'name' => $request['name_agent'],
                    'profile_id' => $formData['profile_id']
                ]);
            }
        }
        $house->moveToStart();
        $files = resolve(Files::class);
        $files->save($house, $request['files']);

        return response()->json([], 201, ['Location' => "/house/$house->id"]);
    }

    public function show(RealtyShowRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $querySearch = $request->querySearch;
        $account = $request->get('accounts');
        if (!empty($querySearch)) {
            event(new SaveLogsEvent($querySearch, (new TypeModules())->job(), auth('api')->user()));
        }
        $house = House::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('city:id,region_id,name', 'city.region:id,full_name', 'image', 'images', 'agent','profile.user','profile.person')
            ->when($cabinet !== false, function ($q) use ($account) {
                $q->whereHas('profile', function ($q) use ($account) {
                    $q->where('id', $account['profile_id']);
                });
            })
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->when($catalog === true, function ($q) use ($states) {
                $q ->whereHas('profile.user', function ($q) use ($states) {
                    $q->where('state', $states->active());
                });
            })
            ->first();

        abort_unless($house, 404);

        $files = resolve(Files::class);
        if (!empty($house->images)) {
            $house->photo = $files->getFilePath($house->latestImage);
            $house->photos = collect([]);
            $house->images->each(function ($image) use ($files, $house) {
                $house->photos->push($files->getFilePath($image));
            });
            $house->photos = array_filter($house->photos->toArray());
        }
        $house->makeHidden('image');
        $house->makeHidden('images');
        $house->title = $house->name;
        $street = str_replace('.', '', $house->street);
        $house->full_address = rtrim(collect([$house->city->region->full_name, $house->city->name, $street, $house->house])->join(', '), ", ");

        return response()->json($house);
    }

    public function update(HouseUpdateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $house = House::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $house->fill($formData);

        $house->update();

        if (isset($request['name_agent'])) {
            $agent = $house->agent;
            if (!empty($agent)) {
                $agent->name = $request['name_agent'];
                $agent->update();
            }
        }

        $files = resolve(Files::class);

        $files->save($house, $request['files']);

        return response()->json([], 204);
    }

    public function state(AdStateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $state = $request->state;
        $ad = House::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $ad->state = $state;
        $ad->update();
        if ($state !== (new States())->active()) {
            $ad->moveToEnd();
        }

        return response()->json([], 204);
    }

    public function sort(string $id): \Illuminate\Http\JsonResponse
    {

        $vacancy = House::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $vacancy->moveToStart();

        return response()->json([]);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $house = House::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (isset($house)) {
            $house->moveToEnd();
            $house->realties()->delete();
            $house->delete();
        }
        return response()->json([], 204);
    }
}
