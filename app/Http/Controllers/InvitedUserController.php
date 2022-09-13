<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitedUserRequest;
use App\Http\Requests\UpdateInvitedUserRequest;
use App\Models\InvitedUser;

class InvitedUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreInvitedUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvitedUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InvitedUser  $invitedUser
     * @return \Illuminate\Http\Response
     */
    public function show(InvitedUser $invitedUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvitedUser  $invitedUser
     * @return \Illuminate\Http\Response
     */
    public function edit(InvitedUser $invitedUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateInvitedUserRequest  $request
     * @param  \App\Models\InvitedUser  $invitedUser
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvitedUserRequest $request, InvitedUser $invitedUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InvitedUser  $invitedUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvitedUser $invitedUser)
    {
        //
    }
}
