<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\JobsVacancyCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoryVacancyController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);

        $builder = JobsVacancyCategory::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('active', 1);

        $vacancyCategory = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('image', 'categories')
            ->get();

        $count = $builder->count();

        $vacancyCategory->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
        });

        $data = (new JsonHelper())->getIndexStructure(new JobsVacancyCategory(), $vacancyCategory, $count, (int) $skip);

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

        $files->save($vacancyCategory, $request['files']);

        return response()->json([], 201, ['Location' => "/category-vacancies/$vacancyCategory->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {

        $vacancyCategory = JobsVacancyCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'categories')
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

        $vacancyCategory = JobsVacancyCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();

        if (!isset($vacancyCategory)) {
            throw new ModelNotFoundException("__('validation.permissionDenied')", Response::HTTP_FORBIDDEN);
        }

        $vacancyCategory->fill($formData);

        $vacancyCategory->update();

        $files = resolve(Files::class);

        $files->save($vacancyCategory, $request['files']);

        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        JobsVacancyCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->delete();
        return response()->json([], 204);
    }
}
