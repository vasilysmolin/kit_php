<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Http\Middleware\VacanciesMiddleware;
use App\Http\Requests\Job\VacancyIndexRequest;
use App\Http\Requests\Job\VacancyShowRequest;
use App\Http\Requests\Job\VacancyStoreRequest;
use App\Http\Requests\Job\VacancyUpdateRequest;
use App\Models\JobsVacancy;
use App\Models\JobsVacancyCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;

class VacancyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api'])->only('store');
        $this->middleware(['auth:api', VacanciesMiddleware::class])->only('destroy', 'update');
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
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $state = $request->state;
        $states = new States();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }
        $buidler = JobsVacancy::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when(!empty($state) && $states->isExists($state), function ($q) use ($state) {
                $q->where('state', $state);
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
            ->orderBy('id', 'DESC');

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
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $vacancy = JobsVacancy::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image')
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
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

    public function update(VacancyUpdateRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        unset($formData['category_id']);
        $vacancy = JobsVacancy::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $vacancy->fill($formData);

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

    public function store(VacancyStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;
        unset($formData['category_id']);
        $vacancy = new JobsVacancy();
        $vacancy->fill($formData);
        $vacancy->save();
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
        JobsVacancy::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->delete($id);
        return response()->json([], 204);
    }
}
