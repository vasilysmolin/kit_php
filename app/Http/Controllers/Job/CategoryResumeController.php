<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\JobsResumeCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoryResumeController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);

        $builder = JobsResumeCategory::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->where('active', 1);

        $resumeCategory = $builder
            ->take((int) $take)
            ->with('image', 'categories')
            ->skip((int) $skip)
            ->get();
        $count = $builder->count();
        $resumeCategory->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
        });

        $data = (new JsonHelper())->getIndexStructure(new JobsResumeCategory(), $resumeCategory, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;

        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        unset($formData['categoryID']);
        $resumeCategory = new JobsResumeCategory();
        $resumeCategory->fill($formData);

        $resumeCategory->save();
        $files = resolve(Files::class);

        $files->save($resumeCategory, $request['files']);

        return response()->json([], 201, ['Location' => "/category-resume/$resumeCategory->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        auth('api')->user();

        $resumeCategory = JobsResumeCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'categories')
            ->first();

        $files = resolve(Files::class);
        if (isset($resumeCategory->image)) {
            $resumeCategory->photo = $files->getFilePath($resumeCategory->image);
        }

        abort_unless($resumeCategory, 404);

        return response()->json($resumeCategory);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $formData['profile_id'] = auth('api')->user()->profile->id;

        if (isset($formData['name'])) {
            $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');
        }
        unset($formData['categoryID']);
        $resumeCategory = JobsResumeCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();

        if (!isset($resumeCategory)) {
            throw new ModelNotFoundException("__('validation.permissionDenied')", Response::HTTP_FORBIDDEN);
        }

        $resumeCategory->fill($formData);

        $resumeCategory->update();

        $files = resolve(Files::class);

        $files->save($resumeCategory, $request['files']);

        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        JobsResumeCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->delete();
        return response()->json([], 204);
    }
}
