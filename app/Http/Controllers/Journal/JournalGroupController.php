<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalGroupRequest;
use App\Http\Requests\UpdateJournalGroupRequest;
use App\Models\JournalGroup;

class JournalGroupController extends Controller
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
     * @param  \App\Http\Requests\StoreJournalGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJournalGroupRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JournalGroup  $journalGroup
     * @return \Illuminate\Http\Response
     */
    public function show(JournalGroup $journalGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JournalGroup  $journalGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(JournalGroup $journalGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateJournalGroupRequest  $request
     * @param  \App\Models\JournalGroup  $journalGroup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJournalGroupRequest $request, JournalGroup $journalGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JournalGroup  $journalGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(JournalGroup $journalGroup)
    {
        //
    }
}
