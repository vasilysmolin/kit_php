<?php

namespace App\Http\Controllers\Job;

use App\Events\SaveLogsEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\StateMiddleware;
use App\Http\Middleware\VacanciesMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Job\VacancyIndexRequest;
use App\Http\Requests\Job\VacancyShowRequest;
use App\Http\Requests\Job\VacancyStateRequest;
use App\Http\Requests\Job\VacancyStoreRequest;
use App\Http\Requests\Job\VacancyUpdateRequest;
use App\Models\JobsVacancy;
use App\Models\JobsVacancyCategory;
use App\Models\Service;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;

class VacancyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
        $this->middleware(['auth:api', VacanciesMiddleware::class])
            ->only('destroy', 'update', 'restore', 'state', 'sort');
        $this->middleware([StateMiddleware::class])
            ->only('state');
    }

    public function index(VacancyIndexRequest $request): \Illuminate\Http\JsonResponse
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryID = $request->category_id;
        $userID = (int) $request->user_id;
        $name = $request->name;
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $state = $request->state;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $skipFromFull = $request->skipFromFull;
        $querySearch = $request->querySearch;
        $vacancyIds = [];

        if (!empty($querySearch)) {
            event(new SaveLogsEvent($querySearch, (new TypeModules())->job(), auth('api')->user()));

            $builder = JobsVacancy::search($querySearch, function ($meilisearch, $query, $options) use ($skipFromFull) {
                if (!empty($skip)) {
                    $options['offset'] = (int) $skipFromFull;
                }
                return $meilisearch->search($query, $options);
            })
                ->take(10000)
                ->orderBy('sort', 'ASC');

            $vacancyIds = $builder->get()->pluck('id');
        }
        $buidler = JobsVacancy::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when(!empty($vacancyIds), function ($query) use ($vacancyIds) {
                $query->whereIn('id', $vacancyIds);
            })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
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

        $vacancy = $buidler
            ->take((int) $take)
            ->skip((int) $skip)
            ->with(['image', 'categories'])
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();

        $count = $buidler->count();

        $vacancy->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->title = $item->name;
        });

        $data = (new JsonHelper())->getIndexStructure(new JobsVacancy(), $vacancy, $count, (int) $skip);

        return response()->json($data);
    }

    public function show(VacancyShowRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';

        $vacancy = JobsVacancy::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image')
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
        if (isset($vacancy->image)) {
            $vacancy->photo = $files->getFilePath($vacancy->image);
//            $vacancy->makeHidden('image');
        }

        abort_unless($vacancy, 404);
        $vacancy->title = $vacancy->name;

        return response()->json($vacancy);
    }

    public function sort($id): \Illuminate\Http\JsonResponse
    {

        $vacancy = JobsVacancy::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $vacancy->moveToStart();

        return response()->json([]);
    }

    public function update(VacancyUpdateRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
//        $currentUser = auth('api')->user();
        unset($formData['state']);
        $vacancy = JobsVacancy::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $vacancy->fill($formData);
//        if (!$currentUser->isAdmin()) {
//            $formData['state'] = (new States())->inProgress();
//            $vacancy->moveToEnd();
//        }
        $vacancy->update();

        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = JobsVacancyCategory::find($request['category_id']);
            if (isset($category)) {
                $vacancy->category_id = $request['category_id'];
                $vacancy->update();
            }
        }

        $files->save($vacancy, $request['files']);


        return response()->json([], 204);
    }

    public function state(VacancyStateRequest $request, $id): \Illuminate\Http\JsonResponse
    {

        $state = $request->state;
        $vacancy = JobsVacancy::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $vacancy->state = $state;
        $vacancy->update();
        if ($state !== (new States())->active()) {
            $vacancy->moveToEnd();
        }

        return response()->json([], 204);
    }

    public function store(VacancyStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = $request->profile_id ?? auth('api')->user()->profile->getKey();
        $formData['active'] = true;
        unset($formData['state']);
        $vacancy = new JobsVacancy();
        $vacancy->fill($formData);
        $vacancy->save();
        $vacancy->moveToStart();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = JobsVacancyCategory::find($request['category_id']);
            if (isset($category)) {
                $vacancy->category_id = $request['category_id'];
                $vacancy->update();
            }
        }

        $files->save($vacancy, $request['files']);

        return response()->json([], 201, ['Location' => "/vacancies/$vacancy->id"]);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $vacancy = JobsVacancy::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->first();
        if (isset($vacancy)) {
            $vacancy->moveToEnd();
            $vacancy->delete();
        }
        return response()->json([], 204);
    }

    public function restore($id): \Illuminate\Http\JsonResponse
    {
        $vacancy = JobsVacancy::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->withTrashed()->first();
        if (isset($vacancy)) {
            $vacancy->moveToStart();
            $vacancy->restore();
        }
        return response()->json([], 204);
    }
}
