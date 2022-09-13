<?php

namespace App\Http\Controllers\Job;

use App\Events\SaveLogsEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\ResumeMiddleware;
use App\Http\Middleware\StateMiddleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Requests\Job\ResumeIndexRequest;
use App\Http\Requests\Job\ResumeShowRequest;
use App\Models\JobsResume;
use App\Models\JobsResumeCategory;
use App\Models\Service;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use App\Objects\TypeModules\TypeModules;
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
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $skipFromFull = $request->skipFromFull;
        $querySearch = $request->querySearch;
        $priceFrom = $request->priceFrom;
        $priceTo = $request->priceTo;
        $resumeIds = [];

        if (!empty($querySearch)) {
            event(new SaveLogsEvent($querySearch, (new TypeModules())->job(), auth('api')->user()));

            $builder = JobsResume::search($querySearch, function ($meilisearch, $query, $options) use ($skipFromFull) {
                if (!empty($skip)) {
                    $options['offset'] = (int) $skipFromFull;
                }
                return $meilisearch->search($query, $options);
            })
                ->take(10000)
                ->orderBy('sort', 'ASC');

            $resumeIds = $builder->get()->pluck('id');
        }


        $builder = JobsResume::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when(!empty($resumeIds), function ($query) use ($resumeIds) {
                $query->whereIn('id', $resumeIds);
            })
            ->when(isset($categoryID), function ($q) use ($categoryID) {
                $q->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('id', $categoryID);
                });
            })
            ->when(!empty($priceFrom), function ($query) use ($priceFrom) {
                $query->where('price', '>=', $priceFrom);
            })
            ->when(!empty($priceTo), function ($query) use ($priceTo) {
                $query->where('price', '<=', $priceTo);
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
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';

        $resume = JobsResume::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'city')
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
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
            ->first();
        abort_unless($resume, 404);
        $files = resolve(Files::class);
        if (isset($resume->image)) {
            $resume->photo = $files->getFilePath($resume->image);
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

        $resume = JobsResume::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
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
