<?php

namespace App\Http\Controllers;

use App\Exports\ExportUsers;
use App\Http\Requests\UsersCheckRequest;
use App\Http\Requests\UsersIndexRequest;
use App\Http\Requests\UsersShowRequest;
use App\Http\Requests\UsersStateRequest;
use App\Http\Requests\UsersUpdateRequest;
use App\Models\InvitedUser;
use App\Models\Profile;
use App\Models\SellerHouse;
use App\Models\User;
use App\Objects\Dadata\Dadata;
use App\Objects\Files;
use App\Objects\JsonHelper;
use App\Objects\States\States;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except(
            'update',
            'show',
            'sellers',
            'checkUser',
            'addUser',
            'deleteUser',
            'accounts',
            'currentAccount',
            'changeProfile'
        );
    }

    public function index(UsersIndexRequest $request)
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $user = auth('api')->user();
        $state = $request->state;
        $name = $request->name;
        $phone = $request->phone;
        $seller = $request->seller;
        $type = $request->type;
        $states = new States();
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';

        $builder = User::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
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
            ->when(!empty($state) && $states->isExists($state), function ($q) use ($state) {
                $q->where('state', $state);
            })
            ->when(!empty($name), function ($q) use ($name) {
                $q->where('name', 'ilike', "%{$name}%");
            })
            ->when(!empty($phone), function ($q) use ($phone) {
                $q->where('phone', 'ilike', "%{$phone}%");
            })
            ->when($seller === 'houses', function ($q) {
                $q->has('profile.houses');
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
            ->with(['profile.person'])
            ->skip((int) $skip)
            ->get();
        $count = $builder->count();

        $data = (new JsonHelper())->getIndexStructure(new User(), $users, $count, (int) $skip);

        return response()->json($data);
    }

    public function sellers(UsersIndexRequest $request)
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $user = auth('api')->user();
//        $state = $request->state;
//        $name = $request->name;
//        $phone = $request->phone;
//        $seller = $request->seller;
//        $type = $request->type;
//        $states = new States();
//        $catalog = $request->from === 'catalog';
//        $cabinet = isset($user) && $request->from === 'cabinet';

        $builder = SellerHouse::
        when(!empty($id) && is_array($id), function ($query) use ($id) {
            $query->whereIn('id', $id);
        })
//            ->when($cabinet === true, function ($q) use ($user) {
//                $q->whereHas('profile.user', function ($q) use ($user) {
//                    $q->where('id', $user->id);
//                });
//            })
//            ->when($catalog === true, function ($q) use ($states) {
//                $q ->whereHas('profile.user', function ($q) use ($states) {
//                    $q->where('state', $states->active());
//                });
//            })
//            ->when(!empty($state) && $states->isExists($state), function ($q) use ($state) {
//                $q->where('state', $state);
//            })
//            ->when(!empty($name), function ($q) use ($name) {
//                $q->where('name', 'ilike', "%{$name}%");
//            })
//            ->when(!empty($phone), function ($q) use ($phone) {
//                $q->where('phone', 'ilike', "%{$phone}%");
//            })
//            ->when($seller === 'houses', function ($q) {
//                $q->has('profile.houses')
//                    ->has('profile.person');
//            })
//            ->when($type === 'physical', function ($q) {
//                $q->whereHas('profile', function ($q) {
//                    $q->where('isPerson', false);
//                });
//            })
//            ->when($type === 'entity', function ($q) {
//                $q->whereHas('profile', function ($q) {
//                    $q->where('isPerson', true);
//                });
//            })

            ->orderBy('id', 'DESC');

        $users = $builder
            ->take((int) $take)
            ->with('profile', function($q) {
                $q->with(['person']);
                $q->withCount(['houses']);
            })
            ->with('label', 'background')
            ->skip((int) $skip)
            ->get();

        $files = resolve(Files::class);
        $users->each(function($user) use ($files){
            $user->logo = !empty($user->label) ? $files->getFilePath($user->label) : null;
            $user->background_image = !empty($user->background) ? $files->getFilePath($user->background) : null;
            $user->makeHidden('label');
            $user->makeHidden('background');
        });

        $count = $builder->count();

        $data = (new JsonHelper())->getIndexStructure(new User(), $users, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(Request $request)
    {
    }

    public function download()
    {
        $users = User::get();
        $collectionToExport = new ExportUsers($users->filter(function ($value) {
            return true;
        })->map(function ($user) {
            return [
                'email' => $user->email,
            ];
        }));

        Excel::store($collectionToExport, "users.csv", 'local');
        $file =  storage_path('app/public/users.csv');
        return response()->download($file)->deleteFileAfterSend(true);
    }

    public function show(UsersShowRequest $request, $id)
    {
        $user = User::find($id);
        $user->setAttribute('role', $user->getRoleNames()->first());
        return response()->json($user->load(['profile.person', 'profile.sellerHouse']));
    }

    public function update(UsersUpdateRequest $request, $id)
    {
        $userData = collect($request->all());

        $user = User::where('id', (int) $id)
            ->first();

        if (!isset($user)) {
            throw new ModelNotFoundException("__('validation.permissionDenied')", Response::HTTP_FORBIDDEN);
        }
        $result = $userData
            ->only(['name','email','phone','city_id'])
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
        $states = new States();
        if ($user->state === $states->step()) {
            if ($user->profile->isPerson === false) {
                if (!empty($user->name) && !empty($user->phone)) {
                    $user->state = $states->new();
                    $user->update();
                }
            } else {
                if (!empty($user->profile->person->inn) && !empty($user->phone)) {
                    $user->state = $states->new();
                    $user->update();
                }
            }
        }



        return response()->json([], 204);
    }

    public function changeProfile(Request $request)
    {
        $user = auth('api')->user();
        $profileID = $request->profile_id;
        if ($user->checkProfile($profileID)) {
            $profile = Profile::find($profileID);
            $token = JWTAuth::customClaims([
                'profile_id' => $profileID,
                'id' => $request->id,
            ])->fromUser($user);
            return response()->json(respondWithTokenAndEntity($token, $profile->isPerson));
        }

        return response()->json([]);
    }

    public function accounts(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $accounts = $user->bindingAccounts;
        $data = (new JsonHelper())->getIndexStructure(new User(), $accounts, $accounts->count(), 0);
        return response()->json($data);
    }

    public function currentAccount(Request $request): \Illuminate\Http\JsonResponse
    {
        $claims = JWTAuth::parseToken()->getPayload()->getClaims();
        if (!empty($claims['profile_id'])) {
            $user = User::whereHas('profile', function ($q) use ($claims) {
                    $q->where('id', $claims['profile_id']->getValue());
            })->with(['profile.restaurant', 'profile.person','city'])->first();
        } else {
            $user = auth('api')->user();
            $user->load(['profile.restaurant', 'profile.person','city']);
        }
        return response()->json($user);
    }

    public function checkUser(UsersCheckRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('email', $request->email)
            ->whereHas('profile', function ($q) {
                $q->where('isPerson', false);
            })
            ->first();
        return response()->json(['success' => !empty($user)]);
    }

    public function addUser(UsersCheckRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $userToInvite = User::select(['email','id as user_id', 'name'])
            ->where('email', $request->email)
            ->whereHas('profile', function ($q) {
                $q->where('isPerson', false);
            })
            ->first();
        $invitedUser = $user->profile->invitedAccounts()->where('email', $request->email)->first();
        if (!empty($invitedUser)) {
            return response()->json(['success' => false]);
        }
        $invitingUser = $user->profile->invitedAccounts()->make($userToInvite->toArray());
        $isSave = $invitingUser->save();
        return response()->json(['success' => $isSave]);
    }

    public function deleteUser(UsersCheckRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        InvitedUser::where('email', $request->email)
            ->whereHas('profile', function ($q) use ($user) {
                $q->where('id', $user->profile->getKey());
            })->delete();
        return response()->json(['success' => true]);
    }

    public function state(UsersStateRequest $request, $id): \Illuminate\Http\JsonResponse
    {

        $state = $request->state;
        $user = User::where('id', $id)->with([
            'profile.vacancies',
            'profile.resume',
            'profile.ads',
            'profile.service',
        ])->first();
        $user->state = $state;
        $user->update();
        $states = new States();
        if ($state !== $states->active()) {
            $user->moveToEnd();
            $user->profile->vacancies->each(function ($item) use ($states, $state) {
                if ($state === $states->block() || $states->reBlock()) {
                    $item->state = $state;
                } else {
                    $item->state = $states->pause();
                }
                $item->state = $states->inProgress();
                $item->update();
                $item->moveToEnd();
            });
            $user->profile->resume->each(function ($item) use ($states, $state) {
                if ($state === $states->block() || $states->reBlock()) {
                    $item->state = $state;
                } else {
                    $item->state = $states->pause();
                }
                $item->update();
                $item->moveToEnd();
            });
            $user->profile->ads->each(function ($item) use ($states, $state) {
                if ($state === $states->block() || $states->reBlock()) {
                    $item->state = $state;
                } else {
                    $item->state = $states->pause();
                }
                $item->update();
                $item->moveToEnd();
            });
            $user->profile->service->each(function ($item) use ($states, $state) {
                if ($state === $states->block() || $states->reBlock()) {
                    $item->state = $state;
                } else {
                    $item->state = $states->pause();
                }
                $item->update();
                $item->moveToEnd();
            });
        }

        return response()->json([], 204);
    }

    public function destroy($id)
    {
    }
}
