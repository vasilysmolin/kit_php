<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\JobsVacancy;
use App\Models\JobsVacancyCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class VacancyController extends Controller
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

        $vacancy = JobsVacancy::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
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
            ->with('image', 'categories')
            ->where('active', 1)
            ->get();


        $vacancy->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
        });

        $count = JobsVacancy::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
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
            ->where('active', 1)
            ->count();

        $data = (new JsonHelper())->getIndexStructure(new JobsVacancy(), $vacancy, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;

//        if (isset($formData['address']) && isset($formData['address']['coords']) && is_array($formData['address']['coords'])) {
//            $formData['latitude'] = $formData['address']['coords'][0] ?? 0;
//            $formData['longitude'] = $formData['address']['coords'][1] ?? 0;
//        }
        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        unset($formData['category_id']);
        $vacancy = new JobsVacancy();
        $vacancy->fill($formData);
//        dd($vacancy);
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

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $vacancy = JobsVacancy::where('id', $id)
            ->with('image')
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->first();

        $files = resolve(Files::class);
        if (isset($vacancy->image)) {
            $vacancy->photo = $files->getFilePath($vacancy->image);
//            $vacancy->makeHidden('image');
        }

        abort_unless($vacancy, 404);

        return response()->json($vacancy);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $user = auth('api')->user();
        $formData['profile_id'] = auth('api')->user()->profile->id;

        if (isset($formData['name'])) {
            $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        }
        unset($formData['category_id']);
        $vacancy = JobsVacancy::where('id', $id)
            ->whereHas('profile.user', function ($q) use ($user) {
                $q->where('id', $user->id);
            })
            ->first();

        if (!isset($vacancy)) {
            throw new ModelNotFoundException("Доступ запрещен", Response::HTTP_FORBIDDEN);
        }

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

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        JobsVacancy::destroy($id);
        return response()->json([], 204);
    }
}
