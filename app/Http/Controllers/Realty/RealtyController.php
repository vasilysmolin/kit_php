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
use App\Models\Profile;
use App\Models\Realty;
use App\Models\RealtyCategory;
use App\Models\RealtyParameter;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleXMLElement;

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
        $realty = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('image', 'categories', 'realtyParameters.filter')
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

    public function store(AdStoreRequest $request): \Illuminate\Http\JsonResponse
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
            $realty->realtyParameters()->sync($filters);
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
        $realty = Realty::where('alias', $id)
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

    public function update(AdUpdateRequest $request, string $id): \Illuminate\Http\JsonResponse
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
            $realty->realtyParameters()->sync($filters);
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
        $user = auth('api')->user();
        $contents = Storage::disk('local')->get('flat.xml');
        $realties = new SimpleXMLElement($contents);
        $account = $request->get('accounts');
        $profileId = $account['profile_id'];
        $profile = Profile::find($profileId);
        $realtiesExternal = $profile->realties()->whereNotNull('external_id')->get();
//        $resultNew = collect([]);
//        $resultUpdate = collect([]);
        foreach ($realties->object as $realty) {
            $data = [];
            $data['title'] = (string) $realty->JKSchema->Name;
            $data['alias'] = Str::slug($realty->JKSchema->Name);
            $data['state'] = (new States())->active();
            $data['price'] = (string)  $realty->BargainTerms->Price;
            $data['sale_price'] = (string)  $realty->BargainTerms->Price;
            $data['description'] = (string) $realty->Description;
            $data['profile_id'] = (int) $profileId;
            $data['city_id'] = (int) $user->city->getKey();
            $data['latitude'] = (string) $realty->Coordinates->Lat;
            $data['longitude'] = (string) $realty->Coordinates->Lng;
            $data['street'] = (string) $realty->Address;
            $data['house'] = (string) $realty->JKSchema->House->Name;
            $data['category_id'] = (int) $request->category_id;
            $data['updated_at'] = now();

//            dd($realty);
            $realtyDB = $realtiesExternal->where('external_id', (int) $realty->JKSchema->Id)->first();
            if (!empty($realtyDB)) {
//                $data['id'] = $model->id;
                $realtyDB->fill($data);
                $realtyDB->update();
                $model = $realtyDB;
//                $resultUpdate->push($data);
            } else {
                $data['created_at'] = now();
                $data['external_id'] = (int) $realty->JKSchema->Id;
                $model = new Realty();
                $model->fill($data);

                $model->save();
//                $resultNew->push($data);
            }
//            dd($realty);
            foreach($realty->Photos->PhotoSchema as $photo) {
                $files = resolve(Files::class);
                $files->saveParser($model, (string) $photo->FullUrl);
            }
            $dataParameters = [];
            $dataParameters['floorsCount'] = (int) $realty->Building->FloorsCount;
            $dataParameters['livingArea'] = (int) $realty->LivingArea;
            $dataParameters['floorNumber'] = (int) $realty->FloorNumber;
            $dataParameters['flatRoomsCount'] = (int) $realty->FlatRoomsCount;
            $dataParameters['totalArea'] = (int) $realty->TotalArea;
            $dataParameters['kitchenArea'] = (int) $realty->KitchenArea;
            $dataParameters['materialType'] = (int) $realty->MaterialType;
            $rooms = RealtyParameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->first();
            $livingArea = RealtyParameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) {
                $q->where('alias', 'zilaya-ploshhad'  . '-rent');
            })->first();
            $kitArea = RealtyParameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) {
                $q->where('alias', 'ploshhad-kuxni'  . '-rent');
            })->first();
            $totalArea = RealtyParameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) {
                $q->where('alias', 'obshhaya-ploshhad'  . '-rent');
            })->first();
            $floor = RealtyParameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) {
                $q->where('alias', 'etaz'  . '-rent');
            })->first();
            $floorsInHouse = RealtyParameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) {
                $q->where('alias', 'vsego-etazei'  . '-rent');
            })->first();

            $arr = collect();
            if (!empty($comfortBal)) {
                $arr->add($comfortBal->getKey());
            }
            if (!empty($comfortTwoLift)) {
                $arr->add($comfortTwoLift->getKey());
            }
            if (!empty($comfortCons)) {
                $arr->add($comfortCons->getKey());
            }
            if (!empty($comfortPhone)) {
                $arr->add($comfortPhone->getKey());
            }
            if (!empty($comfortPark)) {
                $arr->add($comfortPark->getKey());
            }
            if (!empty($comfortNet)) {
                $arr->add($comfortNet->getKey());
            }
            if (!empty($rooms)) {
                $arr->add($rooms->getKey());
            }
            if (!empty($livingArea)) {
                $arr->add($livingArea->getKey());
            }
            if (!empty($kitArea)) {
                $arr->add($kitArea->getKey());
            }
            if (!empty($totalArea)) {
                $arr->add($totalArea->getKey());
            }
            if (!empty($floorsInHouse)) {
                $arr->add($floorsInHouse->getKey());
            }
            if (!empty($floor)) {
                $arr->add($floor->getKey());
            }
            if (!empty($dom)) {
                $arr->add($dom->getKey());
            }
            if (!empty($seller)) {
                $arr->add($seller->getKey());
            }
            if (!empty($novizna)) {
                $arr->add($novizna->getKey());
            }
            $model->realtyParameters()->sync($arr);

        }
//        Realty::insert($resultNew->toArray());
//        Realty::upsert($resultUpdate->toArray(), 'id', [
//            'id',
//            'title',
//            'name',
//            'state',
//            'price',
//            'sale_price',
//            'description',
//            'profile_id',
//            'city_id',
//            'latitude',
//            'longitude',
//            'street',
//            'house',
//            'category_id',
//        ]);
//
//        dd($resultUpdate, $resultNew);

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
