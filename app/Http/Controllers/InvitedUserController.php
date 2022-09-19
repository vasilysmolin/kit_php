<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitedUserRequest;
use App\Http\Requests\UpdateInvitedUserRequest;
use App\Models\InvitedUser;
use App\Models\User;
use App\Objects\JsonHelper;

class InvitedUserController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();
        $profileID = $user->profile->getKey();
        $users = InvitedUser::where('profile_id', $profileID)->get();
        $data = (new JsonHelper())->getIndexStructure(new InvitedUser(), $users, $users->count(), 0);
        return response()->json($data);
    }


    public function create()
    {
    }


    public function store(StoreInvitedUserRequest $request)
    {
    }


    public function show(InvitedUser $invitedUser)
    {
    }


    public function edit(InvitedUser $invitedUser)
    {
    }


    public function update(UpdateInvitedUserRequest $request, InvitedUser $invitedUser)
    {
    }


    public function destroy(InvitedUser $invitedUser)
    {
    }
}
