<?php

namespace App\Http\Controllers\Realty;

use App\Events\SaveLogsEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\RealtyMiddleware;
use App\Http\Middleware\StateMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Ad\AdIndexRequest;
use App\Http\Requests\Ad\AdShowRequest;
use App\Http\Requests\Ad\AdStateRequest;
use App\Http\Requests\Ad\AdStoreRequest;
use App\Http\Requests\Ad\AdUpdateRequest;
use App\Models\Realty;
use App\Models\RealtyCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;

class RealtyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
        $this->middleware(['auth:api', RealtyMiddleware::class])
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
        $priceFrom = $request->priceFrom;
        $priceTo = $request->priceTo;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $filters = $request->filter;
        $skipFromFull = $request->skipFromFull;
        $querySearch = $request->querySearch;
        $account = $request->get('accounts');
        $RealtyIds = [];

        if (!empty($querySearch)) {
            event(new SaveLogsEvent($querySearch, (new TypeModules())->job(), auth('api')->user()));

            $builder = Realty::search($querySearch, function ($meilisearch, $query, $options) use ($skipFromFull) {
                if (!empty($skip)) {
                    $options['offset'] = (int) $skipFromFull;
                }
                return $meilisearch->search($query, $options);
            })
                ->take(10000)
                ->orderBy('sort', 'ASC');

            $RealtyIds = $builder->get()->pluck('id');
        }
        $builder = Realty::when(!empty($id) && is_array($id), function ($query) use ($id) {
            $query->whereIn('id', $id);
        })
            ->when(!empty($RealtyIds), function ($query) use ($RealtyIds) {
                $query->whereIn('id', $RealtyIds);
            })
            ->when(!empty($priceFrom), function ($query) use ($priceFrom) {
                $query->where('sale_price', '>=', $priceFrom);
            })
            ->when(!empty($priceTo), function ($query) use ($priceTo) {
                $query->where('sale_price', '<=', $priceTo);
            })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when(isset($filters), function ($q) use ($filters) {
                $q->whereHas('realtyParameters', function ($q) use ($filters) {
                    $q->whereIn('id', $filters);
                }, '=', count($filters));
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
        $Realty = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('image', 'categories', 'realtyParameters.filter')
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();
        $count = $builderCount->count();

        $Realty->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
                $item->title = $item->name;
        });
        $data = (new JsonHelper())->getIndexStructure(new Realty(), $Realty, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(AdStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $account = $request->get('accounts');
        $formData['profile_id'] = $account['profile_id'];
        $formData['active'] = true;
        $filters = $request->filter;

        unset($formData['category_id']);
        $Realty = new Realty();
        $Realty->fill($formData);
        $Realty->save();
        $Realty->moveToStart();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = RealtyCategory::find($request['category_id']);
            if (isset($category)) {
                $Realty->category_id = $request['category_id'];
                $Realty->update();
            }
        }

        $files->save($Realty, $request['files']);
        if (!empty($filters)) {
            $Realty->realtyParameters()->sync($filters);
        }

        return response()->json([], 201, ['Location' => "/declarations/$Realty->id"]);
    }

    public function sort(string $id): \Illuminate\Http\JsonResponse
    {

        $vacancy = Realty::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $vacancy->moveToStart();

        return response()->json([]);
    }

    public function show(AdShowRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        $Realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'images', 'realtyParameters.filter', 'city')
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

        abort_unless($Realty, 404);

        $files = resolve(Files::class);
        if (isset($Realty->image)) {
            $Realty->photo = $files->getFilePath($Realty->image);
        }
        if (!empty($Realty->images)) {
            $Realty->photos = collect([]);
            $Realty->images->each(function ($image) use ($files, $Realty) {
                $Realty->photos->push($files->getFilePath($image));
            });
        }
        $Realty->makeHidden('image');
        $Realty->makeHidden('images');
        $Realty->title = $Realty->name;

        return response()->json($Realty);
    }

    public function update(AdUpdateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $filters = $request->filter;
        unset($formData['category_id']);

        $Realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $Realty->fill($formData);

        $Realty->update();
        if (!empty($filters)) {
            $Realty->realtyParameters()->sync($filters);
        }

        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = RealtyCategory::find($request['category_id']);
            if (isset($category)) {
                $Realty->category_id = $request['category_id'];
                $Realty->update();
            }
        }

        $files->save($Realty, $request['files']);

        return response()->json([], 204);
    }

    public function state(AdStateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $state = $request->state;
        $ad = Realty::
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

    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $Realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (isset($Realty)) {
            $Realty->moveToEnd();
            $Realty->delete();
        }
        return response()->json([], 204);
    }

    public function restore(string $id): \Illuminate\Http\JsonResponse
    {
        $Realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->withTrashed()->first();
        if (isset($Realty)) {
            $Realty->moveToStart();
            $Realty->restore();
        }
        return response()->json([], 204);
    }

    private function iter(?RealtyCategory $item, array $acc): array
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
