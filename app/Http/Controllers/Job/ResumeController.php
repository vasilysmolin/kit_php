<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\JobsResume;
use App\Models\JobsResumeCategory;
use App\Objects\Files;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ResumeController extends Controller
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

        $resume = JobsResume::take((int) $take)
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


        $resume->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
        });

        $count = JobsResume::take((int) $take)
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
            'resume' => $resume,
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
        $resume = new JobsResume();
        $resume->fill($formData);
//        dd($resume);
        $resume->save();
        $files = resolve(Files::class);

        if (isset($request['categoryID'])) {
            $category = JobsResumeCategory::find($request['categoryID']);
            if (isset($category)) {
                $resume->categories()->save($category);
            }
        }

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $resume->image()->create([
                    'mimeType' => $dataFile['mineType'],
                    'extension' => $dataFile['extension'],
                    'name' => $dataFile['name'],
                    'uniqueValue' => $dataFile['name'],
                    'size' => $dataFile['size'],
                ]);
            }
        }

        return response()->json([], 201, ['Location' => "/resumes/$resume->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $resume = JobsResume::where('id', $id)
            ->with('image', 'categories:id')
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->first();

        $files = resolve(Files::class);
        if (isset($resume->image)) {
            $resume->photo = $files->getFilePath($resume->image);
//            $resume->makeHidden('image');
        }

        abort_unless($resume, 404);

        return response()->json($resume);
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
        $resume = JobsResume::where('id', $id)
            ->whereHas('profile.user', function ($q) use ($user) {
                $q->where('id', $user->id);
            })
            ->first();

        if (!isset($resume)) {
            throw new ModelNotFoundException("Доступ запрещен", Response::HTTP_FORBIDDEN);
        }

        $resume->fill($formData);

        $resume->update();

        $files = resolve(Files::class);

        if (isset($request['categoryID'])) {
            $category = JobsResumeCategory::find($request['categoryID']);
            if (isset($category)) {
                $resume->categories()->save($category);
            }
        }

        if (isset($request['files']) && count($request['files']) > 0) {
            foreach ($request['files'] as $file) {
                $dataFile = $files->preparationFileS3($file);
                $resume->image()->create([
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
        JobsResume::destroy($id);
        return response()->json([], 204);
    }
}
