<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersIndexRequest;
use App\Http\Requests\UsersShowRequest;
use App\Http\Requests\UsersUpdateRequest;
use App\Models\User;
use App\Objects\Dadata\Dadata;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin')->except('update', 'show');
    }

    public function index(UsersIndexRequest $request)
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $user = auth('api')->user();
        $state = $request->state;
        $name = $request->name;
        $type = $request->type;
        $states = new States();
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
            ->when(!empty($state) && $states->isExists($state), function ($q) use ($state) {
                $q->where('state', $state);
            })
            ->when(!empty($name), function ($q) use ($name) {
                $q->where('name', 'ilike', "%{$name}%");
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

    public function show(UsersShowRequest $request, $id)
    {

        $user = User::find($id);

        $user->setAttribute('role', $user->getRoleNames()->first());
        return response()->json($user->load(['profile.person']));
    }

    public function update(UsersUpdateRequest $request, $id)
    {
        $userData = collect($request->all());

        $user = User::where('id', (int) $id)
            ->first();

        if (!isset($user)) {
            throw new ModelNotFoundException("Доступ запрещен", Response::HTTP_FORBIDDEN);
        }
        $result = $userData
            ->only(['name','email','phone','state'])->filter(function ($item) {
                return $item !== null;
            })
            ->all();

        DB::transaction(function () use ($user, $result, $userData) {
            $user->fill($result)->update();
            if (!empty($userData['profile'])) {
                $isPerson = collect($userData['profile'])->only(['isPerson'])->all();
                $user->profile->fill($isPerson)->update();
                $person = collect($userData['person'])->only(['inn','name'])->all();
                if (!empty($person) && !empty($person['inn'])) {
                    $dadata = new Dadata();
                    $companies = $dadata->findCompany($person['inn']);
                    $person['name'] = $dadata->getCompanyName($companies);
                    if (empty($user->profile->person)) {
                        $user->profile->person()->create($person);
                    } else {
                        $user->profile->person->fill($person)->update();
                    }
                }
            }
        }, 3);


        return response()->json([], 204);
    }


    public function destroy($id)
    {
    }
}
