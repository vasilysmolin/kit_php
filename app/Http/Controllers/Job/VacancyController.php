<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\JobsVacancy;
use App\Models\JobsVacancyCategory;
use App\Objects\Files;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class VacancyController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryID = $request->categoryID;
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
            ->when(isset($categoryRestaurantID), function ($q) use ($categoryID) {
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

        $data = [
            'meta' => [
                'skip' => (int) $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0,
            ],
            'vacancy' => $vacancy,
        ];

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
        unset($formData['categoryID']);
        $vacancy = new JobsVacancy();
        $vacancy->fill($formData);
//        dd($vacancy);
        $vacancy->save();
        $files = resolve(Files::class);

        if (isset($request['categoryID'])) {
            $category = JobsVacancyCategory::find($request['categoryID']);
            if (isset($category)) {
                $vacancy->categories()->save($category);
            }
        }

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $vacancy->image()->create([
                    'mimeType' => $dataFile['mineType'],
                    'extension' => $dataFile['extension'],
                    'name' => $dataFile['name'],
                    'uniqueValue' => $dataFile['name'],
                    'size' => $dataFile['size'],
                ]);
            }
        }

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
            ->with('image', 'categories:id')
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
        unset($formData['categoryID']);
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

        if (isset($request['categoryID'])) {
            $category = JobsVacancyCategory::find($request['categoryID']);
            if (isset($category)) {
                $vacancy->categories()->save($category);
            }
        }

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $vacancy->image()->create([
                    'mimeType' => $dataFile['mineType'],
                    'extension' => $dataFile['extension'],
                    'name' => $dataFile['name'],
                    'uniqueValue' => $dataFile['name'],
                    'size' => $dataFile['size'],
                ]);
            }
        }


        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        JobsVacancy::destroy($id);
        return response()->json([], 204);
    }
}
