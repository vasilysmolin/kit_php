<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResumeMiddleware;
use App\Http\Middleware\StateMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Job\ResumeIndexRequest;
use App\Http\Requests\Job\ResumeShowRequest;
use App\Models\JobsResume;
use App\Models\JobsResumeCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use Illuminate\Http\Request;

class ResumeController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api', StoreMiddleware::class])
            ->only('store');
        $this->middleware(['auth:api', ResumeMiddleware::class])
            ->only('destroy', 'update', 'restore', 'state', 'sort');
        $this->middleware([StateMiddleware::class])
            ->only('state');
    }

    public function index(ResumeIndexRequest $request): \Illuminate\Http\JsonResponse
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
        $states = new States();
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $builder = JobsResume::
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
            ->when(!empty($name), function ($q) use ($name) {
                $q->where('name', 'ilike', "%{$name}%");
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
            ->orderBy('sort', 'ASC');

        $resume = $builder
            ->take((int) $take)
            ->with('categories', 'image')
            ->skip((int) $skip)
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();
        $count = $builder->count();
        $resume->each(function ($item) use ($files) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            $item->title = $item->name;
        });

        $data = (new JsonHelper())->getIndexStructure(new JobsResume(), $resume, $count, (int) $skip);

        return response()->json($data);
    }

    public function show(ResumeShowRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $expand = $request->expand ? explode(',', $request->expand) : null;
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $resume = JobsResume::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image')
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->first();
        abort_unless($resume, 404);
        $files = resolve(Files::class);
        if (isset($resume->image)) {
            $resume->photo = $files->getFilePath($resume->image);
//            $resume->makeHidden('image');
        }

        $resume->title = $resume->name;

        return response()->json($resume);
    }

    public function sort($id): \Illuminate\Http\JsonResponse
    {

        $resume = JobsResume::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $resume->moveToStart();

        return response()->json([]);
    }

    public function state(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $state = $request->state;
        $resume = JobsResume::
        where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        $resume->state = $state;
        $resume->update();
        if ($state !== (new States())->active()) {
            $resume->moveToEnd();
        }

        return response()->json([], 204);
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
        $resume->moveToStart();
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
        $currentUser = auth('api')->user();

        $resume = JobsResume::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (!$currentUser->isAdmin()) {
            $formData['state'] = (new States())->inProgress();
            $resume->moveToEnd();
        }
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
        $resume = JobsResume::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->first();

        if (isset($resume)) {
            $resume->moveToEnd();
            $resume->delete();
        }
        return response()->json([], 204);
    }

    public function restore($id): \Illuminate\Http\JsonResponse
    {
        $resume = JobsResume::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })->withTrashed()->first();
        if (isset($resume)) {
            $resume->moveToStart();
            $resume->restore();
        }
        return response()->json([], 204);
    }
}
