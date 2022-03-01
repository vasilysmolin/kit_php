<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResumeMiddleware;
use App\Models\JobsResume;
use App\Models\JobsResumeCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api'])->only('store');
        $this->middleware(['auth:api', ResumeMiddleware::class])->only('destroy', 'update');
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $user = auth('api')->user();
        $categoryID = $request->category_id;
        $userID = (int) $request->user_id;
        $status = $request->status;
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
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when($status !== null, function ($q) use ($status) {
                $q->where('state', $status);
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
            ->orderBy('id', 'DESC')
            ->with('categories', 'image')
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
            ->when($status !== null, function ($q) use ($status) {
                $q->where('state', $status);
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
            ->where('active', 1)
            ->count();

        $data = (new JsonHelper())->getIndexStructure(new JobsResume(), $resume, $count, (int) $skip);

        return response()->json($data);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $resume = JobsResume::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->with('image')
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

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        $formData['profile_id'] = auth('api')->user()->profile->id;

        $formData['active'] = true;
        unset($formData['category_id']);
        $resume = new JobsResume();
        $resume->fill($formData);
        $resume->save();
        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = JobsResumeCategory::find($request['category_id']);
            if (isset($category)) {
                $resume->category_id = $request['category_id'];
                $resume->update();
            }
        }

        $files->save($resume, $request['files']);

        return response()->json([], 201, ['Location' => "/resumes/$resume->id"]);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();

        unset($formData['category_id']);
        $resume = JobsResume::where('alias', $id)
            ->orWhere('id', (int) $id)
            ->first();

        $resume->fill($formData);

        $resume->update();

        $files = resolve(Files::class);

        if (isset($request['category_id'])) {
            $category = JobsResumeCategory::find($request['category_id']);
            if (isset($category)) {
                $resume->category_id = $request['category_id'];
                $resume->update();
            }
        }

        $files->save($resume, $request['files']);

        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        JobsResume::where('alias', $id)
            ->orWhere('id', (int) $id)->delete();
        return response()->json([], 204);
    }
}
