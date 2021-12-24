<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\JobsVacancyCategory;
use App\Objects\Files;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoryVacancyController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? 25;
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);

        $vacancyCategory = JobsVacancyCategory::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->with('image')
            ->where('active', 1)
            ->get();


        $vacancyCategory->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
        });

        $count = JobsVacancyCategory::take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('active', 1)
            ->count();

        $data = [
            'meta' => [
                'skip' => (int) $skip ?? 0,
                'limit' => 25,
                'total' => $count ?? 0,
            ],
            'category_vacancies' => $vacancyCategory,
        ];

        return response()->json($data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;

        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');

        $vacancyCategory = new JobsVacancyCategory();
        $vacancyCategory->fill($formData);

        $vacancyCategory->save();
        $files = resolve(Files::class);

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $vacancyCategory->image()->create([
                    'mimeType' => $dataFile['mineType'],
                    'extension' => $dataFile['extension'],
                    'name' => $dataFile['name'],
                    'uniqueValue' => $dataFile['name'],
                    'size' => $dataFile['size'],
                ]);
            }
        }

        return response()->json([], 201, ['Location' => "/category-vacancies/$vacancyCategory->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {

        $vacancyCategory = JobsVacancyCategory::where('id', $id)
            ->with('image')
            ->first();

        $files = resolve(Files::class);
        if (isset($vacancyCategory->image)) {
            $vacancyCategory->photo = $files->getFilePath($vacancyCategory->image);
        }

        abort_unless($vacancyCategory, 404);

        return response()->json($vacancyCategory);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $formData['profile_id'] = auth('api')->user()->profile->id;

        if (isset($formData['name'])) {
            $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        }

        $vacancyCategory = JobsVacancyCategory::where('id', $id)
            ->first();

        if (!isset($vacancyCategory)) {
            throw new ModelNotFoundException("Доступ запрещен", Response::HTTP_FORBIDDEN);
        }

        $vacancyCategory->fill($formData);

        $vacancyCategory->update();

        $files = resolve(Files::class);

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $vacancyCategory->image()->create([
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
        JobsVacancyCategory::destroy($id);
        return response()->json([], 204);
    }
}
