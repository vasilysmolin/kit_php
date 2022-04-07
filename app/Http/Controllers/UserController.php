<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Objects\JsonHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin', ['except' => ['update']]);
    }

    public function index(Request $request)
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $user = auth('api')->user();
        $status = $request->status;
        $type = $request->type;
        if (isset($user) && $request->from === 'cabinet') {
            $cabinet = true;
        } else {
            $cabinet = false;
        }

        $builder = User::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($cabinet !== false, function ($q) use ($user) {
                $q->whereHas('profile.user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            })
            ->when($status !== null, function ($q) use ($status) {
                $q->where('state', $status);
            })
            ->when($type === 'physical', function ($q) {
                $q->whereHas('profile', function ($q) {
                    $q->where('isPerson', false);
                });
            })
            ->when($type === 'entity', function ($q) {
                $q->whereHas('profile', function ($q) {
                    $q->where('isPerson', true);
                });
            })
            ->orderBy('id', 'DESC');

        $users = $builder
            ->take((int) $take)
            ->with(['profile.restaurant', 'profile.person'])
            ->skip((int) $skip)
            ->get();
        $count = $builder->count();

        $data = (new JsonHelper())->getIndexStructure(new User(), $users, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(Request $request)
    {
    }

    public function show(Request $request, $id)
    {

        $user = User::find($id);

        $user->setAttribute('role', $user->getRoleNames()->first());
        return response()->json($user->load(['profile.restaurant', 'profile.person']));
    }

    public function update(Request $request, $id)
    {
        $formData = collect($request->all());

        $user = User::where('id', (int) $id)
            ->first();

        if (!isset($user)) {
            throw new ModelNotFoundException("Доступ запрещен", Response::HTTP_FORBIDDEN);
        }
        $result = $formData
            ->only(['name','email','phone','state'])
            ->all();

        $user->fill($result);

        $user->update();

        return response()->json([], 204);
    }


    public function destroy($id)
    {
    }
}
