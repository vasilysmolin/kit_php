<?php

namespace App\Http\Controllers\Ad;

use App\Events\SaveLogsEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\External\YandexMapController;
use App\Http\Middleware\AdMiddleware;
use App\Http\Middleware\StateMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Ad\AdIndexRequest;
use App\Http\Requests\Ad\AdShowRequest;
use App\Models\CatalogAd;
use App\Models\CatalogAdCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
        $this->middleware(['auth:api', AdMiddleware::class])
            ->only('destroy', 'update', 'restore', 'state', 'sort');
        $this->middleware([StateMiddleware::class])
            ->only('state');
    }

    public function index(AdIndexRequest $request): \Illuminate\Http\JsonResponse
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryID = $request->category_id;
        $userID = (int) $request->user_id;
        $state = $request->state;
        $name = $request->name;
        $alias = $request->alias;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $filters = $request->filter;
        $querySearch = $request->querySearch;
        $catalogAdIds = [];

        if (!empty($querySearch)) {
            event(new SaveLogsEvent($querySearch, (new TypeModules())->job(), auth('api')->user()));

            $builder = CatalogAd::search($querySearch)
                ->take(100000)
                ->orderBy('sort', 'ASC');

            $catalogAdIds = $builder->get()->pluck('id');
        }

        $builder = CatalogAd::when(!empty($id) && is_array($id), function ($query) use ($id) {
            $query->whereIn('id', $id);
        })
            ->when(!empty($catalogAdIds), function ($query) use ($catalogAdIds) {
                $query->whereIn('id', $catalogAdIds);
            })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when(isset($filters), function ($q) use ($filters) {
                $q->whereHas('adParameters', function ($q) use ($filters) {
                    $q->where('id', $filters);
                });
            })
            ->when(!empty($alias), function ($query) use ($alias) {
                $category = CatalogAdCategory::where('alias', $alias)
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
            ->orderBy('sort', 'ASC');

        $builderCount = clone $builder;
        $catalogAd = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('image', 'categories', 'adParameters.filter')
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();
        $count = $builderCount->count();

        $catalogAd->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
                $item->title = $item->name;
        });

        $data = (new JsonHelper())->getIndexStructure(new CatalogAd(), $catalogAd, $count, (int) $skip);

        return response()->json($data);
    }

    public function fullSearch(AdIndexRequest $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take;
        $skip = $request->skip ?? 0;
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $files = resolve(Files::class);

        $state = $request->state;
        $states = new States();

        $builder = CatalogAd::search($request->get('querySearch'))->
            when(!empty($state) && $states->isExists($state), function ($q) use ($state) {
                $q->where('state', $state);
            })
            ->when(!empty($take), function ($query) use ($take) {
                $query->take((int) $take);
            })
            ->orderBy('sort', 'ASC');

        $catalogAd = $builder->get();

        $catalogAd->load('image', 'categories')->when(!empty($expand), function ($q) use ($expand) {
            $q->load($expand);
        });
        $count = $builder->count();

        $catalogAd->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->title = $item->name;
        });

        $data = (new JsonHelper())->getIndexStructure(new CatalogAd(), $catalogAd, $count, (int) $skip);

        return response()->json(1);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;
        $filters = $request->filter;

        if (isset($formData['longitude']) && isset($formData['latitude'])) {
            $yandexMap = resolve(YandexMapController::class);
            $yandexMapData = $yandexMap->getAddressByCoords($formData['longitude'], $formData['latitude']);
            $formData['street'] = $yandexMapData['ThoroughfareName'] ?? null;
            $formData['house'] = $yandexMapData['PremiseNumber'] ?? null;
        }

        unset($formData['category_id']);
        $catalogAd = new CatalogAd();
        $catalogAd->fill($formData);
        $catalogAd->save();
        $catalogAd->moveToStart();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = CatalogAdCategory::find($request['category_id']);
            if (isset($category)) {
                $catalogAd->category_id = $request['category_id'];
                $catalogAd->update();
            }
        }

        $files->save($catalogAd, $request['files']);
        if (!empty($filters)) {
            $catalogAd->adParameters()->sync($filters);
        }

        return response()->json([], 201, ['Location' => "/declarations/$catalogAd->id"]);
    }

    public function sort($id): \Illuminate\Http\JsonResponse
    {

        $vacancy = CatalogAd::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $vacancy->moveToStart();

        return response()->json([]);
    }

    public function show(AdShowRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $catalogAd = CatalogAd::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'images', 'adParameters.filter', 'city')
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->getKey());
                });
            })
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
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
            ->first();

        abort_unless($catalogAd, 404);

        $files = resolve(Files::class);
        if (isset($catalogAd->image)) {
            $catalogAd->photo = $files->getFilePath($catalogAd->image);
        }
        if (!empty($catalogAd->images)) {
            $catalogAd->photos = collect([]);
            $catalogAd->images->each(function ($image) use ($files, $catalogAd) {
                $catalogAd->photos->push($files->getFilePath($image));
            });
        }
        $catalogAd->makeHidden('image');
        $catalogAd->makeHidden('images');
        $catalogAd->title = $catalogAd->name;

        return response()->json($catalogAd);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $filters = $request->filter;
//      $currentUser = auth('api')->user();
        unset($formData['category_id']);
        if (isset($formData['longitude']) && isset($formData['latitude'])) {
            $yandexMap = resolve(YandexMapController::class);
            $yandexMapData = $yandexMap->getAddressByCoords($formData['longitude'], $formData['latitude']);
            $formData['street'] = $yandexMapData['ThoroughfareName'] ?? null;
            $formData['house'] = $yandexMapData['PremiseNumber'] ?? null;
        }

        $catalogAd = CatalogAd::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
//            ->whereHas('profile.user', function ($q) use ($user) {
//                $q->where('id', $user->id);
//            })
            ->first();
        $catalogAd->fill($formData);
//        if (!$currentUser->isAdmin()) {
//            $formData['state'] = (new States())->inProgress();
//            $catalogAd->moveToEnd();
//        }

        $catalogAd->update();
        if (!empty($filters)) {
            $catalogAd->adParameters()->sync($filters);
        }


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

    public function state(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $state = $request->state;
        $ad = CatalogAd::
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

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $catalogAd = CatalogAd::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (isset($catalogAd)) {
            $catalogAd->moveToEnd();
            $catalogAd->delete();
        }
        return response()->json([], 204);
    }

    public function restore($id): \Illuminate\Http\JsonResponse
    {
        $catalogAd = CatalogAd::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->withTrashed()->first();
        if (isset($catalogAd)) {
            $catalogAd->moveToStart();
            $catalogAd->restore();
        }
        return response()->json([], 204);
    }

    private function iter($item, $acc)
    {
        array_push($acc, $item->getKey());
        if (empty($item->categories)) {
            return array_values($acc);
        }
        return $item->categories->reduce(function ($carry, $category) {
            $carry[] = $category->getKey();
            $carry = array_unique($carry);
            return $this->iter($category, $carry);
        }, $acc);
    }
}
