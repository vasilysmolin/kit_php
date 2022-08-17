<?php

namespace App\Http\Controllers\Service;

use App\Events\SaveLogsEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\ServiceMiddleware;
use App\Http\Middleware\StateMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Service\ServiceIndexRequest;
use App\Http\Requests\Service\ServiceShowRequest;
use App\Http\Requests\Service\ServiceUpdateRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
        $this->middleware(['auth:api', ServiceMiddleware::class])
            ->only('destroy', 'update', 'restore', 'state', 'sort');
        $this->middleware([StateMiddleware::class])
            ->only('state');
    }

    public function index(ServiceIndexRequest $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryID = $request->category_id;
        $name = $request->name;
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $userID = (int) $request->user_id;
        $state = $request->state;
        $type = $request->type;
        $alias = $request->alias;
        $priceFrom = $request->priceFrom;
        $priceTo = $request->priceTo;
        $states = new States();
        $skipFromFull = $request->skipFromFull;
        $querySearch = $request->querySearch;
        $serviceIds = [];

        if (!empty($querySearch)) {
            event(new SaveLogsEvent($querySearch, (new TypeModules())->job(), auth('api')->user()));

            $builder = Service::search($querySearch, function ($meilisearch, $query, $options) use ($skipFromFull) {
                if (!empty($skip)) {
                    $options['offset'] = (int) $skipFromFull;
                }
                return $meilisearch->search($query, $options);
            })
                ->take(10000)
                ->orderBy('sort', 'ASC');

            $serviceIds = $builder->get()->pluck('id');
        }

        $builder = Service::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when(!empty($serviceIds), function ($query) use ($serviceIds) {
                $query->whereIn('id', $serviceIds);
            })
            ->when(!empty($priceFrom), function ($query) use ($priceFrom) {
                $query->where('price', '>=', $priceFrom);
            })
            ->when(!empty($priceTo), function ($query) use ($priceTo) {
                $query->where('price', '<=', $priceTo);
            })
            ->when(!empty($type), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when(!empty($name), function ($q) use ($name) {
                $q->where('name', 'ilike', "%{$name}%");
            })
            ->when(!empty($state) && $states->isExists($state), function ($q) use ($state) {
                $q->where('state', $state);
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
            ->when(!empty($alias), function ($query) use ($alias) {
                $category = ServiceCategory::where('alias', $alias)
                    ->with('categories')
                    ->first();
                if (!empty($category)) {
                    $categoriesID = $this->iter($category, []);
                    $query->whereHas('categories', function ($q) use ($categoriesID) {
                        $q->whereIn('id', $categoriesID);
                    });
                }
            })
            ->orderBy('sort', 'ASC');

        $service = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('image', 'categories')
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();

        $count = $builder->count();

        $service->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->title = $item->name;
        });

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
        $service->save();
        $service->moveToStart();
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

    public function show(ServiceShowRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';

        $service = Service::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'city')
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
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->first();

        $files = resolve(Files::class);
        if (isset($service->image)) {
            $service->photo = $files->getFilePath($service->image);
//            $service->makeHidden('image');
        }

        abort_unless($service, 404);
        $service->title = $service->name;

        return response()->json($service);
    }

    public function sort(string $id): \Illuminate\Http\JsonResponse
    {

        $service = Service::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $service->moveToStart();

        return response()->json([]);
    }

    public function update(ServiceUpdateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $currentUser = auth('api')->user();

        unset($formData['category_id']);
        $service = Service::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
//            ->whereHas('profile.user', function ($q) use ($user) {
//                $q->where('id', $user->id);
//            })
            ->first();
//        if (!$currentUser->isAdmin()) {
//            $formData['state'] = (new States())->inProgress();
//            $service->moveToEnd();
//        }
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

    public function state(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $state = $request->state;
        $service = Service::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $service->state = $state;
        $service->update();
        if ($state !== (new States())->active()) {
            $service->moveToEnd();
        }

        return response()->json([], 204);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $service = Service::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (isset($service)) {
            $service->moveToEnd();
            $service->delete();
        }
        return response()->json([], 204);
    }

    public function restore(string $id): \Illuminate\Http\JsonResponse
    {
        $service = Service::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->withTrashed()->first();
        if (isset($service)) {
            $service->moveToStart();
            $service->restore();
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
