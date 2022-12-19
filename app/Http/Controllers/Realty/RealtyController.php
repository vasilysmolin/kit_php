<?php

namespace App\Http\Controllers\Realty;

use App\Events\SaveLogsEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\RealtyMiddleware;
use App\Http\Middleware\StateMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Ad\AdStateRequest;
use App\Http\Requests\Realty\RealtyIndexRequest;
use App\Http\Requests\Realty\RealtyShowRequest;
use App\Http\Requests\Realty\RealtyStoreRequest;
use App\Http\Requests\Realty\RealtyUpdateRequest;
use App\Models\Feed;
use App\Models\Profile;
use App\Models\Realty;
use App\Models\RealtyCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;
use App\Services\ImportFeedService;
use Illuminate\Http\Request;

class RealtyController extends Controller
{
    protected ImportFeedService $importFeedService;

    public function __construct(ImportFeedService $importFeedService)
    {
        $this->importFeedService = $importFeedService;
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
        $this->middleware(['auth:api', RealtyMiddleware::class])
            ->only('destroy', 'update', 'restore', 'state', 'sort');
        $this->middleware([StateMiddleware::class])
            ->only('state');
    }

    public function index(RealtyIndexRequest $request): \Illuminate\Http\JsonResponse
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
        $realtyIds = [];

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

            $realtyIds = $builder->get()->pluck('id');
        }
        $builder = Realty::when(!empty($id) && is_array($id), function ($query) use ($id) {
            $query->whereIn('id', $id);
        })
            ->when(!empty($realtyIds), function ($query) use ($realtyIds) {
                $query->whereIn('id', $realtyIds);
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
                $q->whereHas('parameters', function ($q) use ($filters) {
                    $q->whereIn('parameters.id', $filters);
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
        $realty = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('image', 'categories', 'parameters.filter', 'agent')
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();
        $count = $builderCount->count();

        $realty->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
                $item->title = $item->name;
        });
        $data = (new JsonHelper())->getIndexStructure(new Realty(), $realty, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(RealtyStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $account = $request->get('accounts');
        $formData['profile_id'] = $account['profile_id'];
        $formData['active'] = true;
        $filters = $request->filter;

        unset($formData['category_id']);
        $realty = new Realty();
        $realty->fill($formData);
        $realty->save();
        $realty->moveToStart();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = RealtyCategory::find($request['category_id']);
            if (isset($category)) {
                $realty->category_id = $request['category_id'];
                $realty->update();
            }
        }

        $files->save($realty, $request['files']);
        if (!empty($filters)) {
            $realty->parameters()->sync($filters);
        }

        return response()->json([], 201, ['Location' => "/realties/$realty->id"]);
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
        $realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'images', 'parameters.filter', 'city', 'agent')
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

        abort_unless($realty, 404);

        $files = resolve(Files::class);
        if (isset($realty->image)) {
            $realty->photo = $files->getFilePath($realty->image);
        }
        if (!empty($realty->images)) {
            $realty->photos = collect([]);
            $realty->images->each(function ($image) use ($files, $realty) {
                $realty->photos->push($files->getFilePath($image));
            });
        }
        $realty->makeHidden('image');
        $realty->makeHidden('images');
        $realty->title = $realty->name;

        return response()->json($realty);
    }

    public function update(RealtyUpdateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $filters = $request->filter;
        unset($formData['category_id']);

        $realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $realty->fill($formData);

        $realty->update();
        if (!empty($filters)) {
            $realty->parameters()->sync($filters);
        }

        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = RealtyCategory::find($request['category_id']);
            if (isset($category)) {
                $realty->category_id = $request['category_id'];
                $realty->update();
            }
        }

        $files->save($realty, $request['files']);

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
        $realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (isset($realty)) {
            $realty->moveToEnd();
            $realty->delete();
        }
        return response()->json([], 204);
    }

    public function restore(string $id): \Illuminate\Http\JsonResponse
    {
        $realty = Realty::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->withTrashed()->first();
        if (isset($realty)) {
            $realty->moveToStart();
            $realty->restore();
        }
        return response()->json([], 204);
    }

    public function import(Request $request): \Illuminate\Http\JsonResponse
    {
        $feed = Feed::find($request->id);
        $account = $request->get('accounts');
        $profileId = $account['profile_id'];
        $profile = Profile::find($profileId);
        $this->importFeedService->import($feed, $profile);
        return response()->json(['successText' => __('import.imProcess')], 204);
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
