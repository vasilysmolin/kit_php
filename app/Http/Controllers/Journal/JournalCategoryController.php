<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalCategoryRequest;
use App\Http\Requests\UpdateJournalCategoryRequest;
use App\Models\JournalCategory;

class JournalCategoryController extends Controller
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
     * @param  \App\Http\Requests\StoreJournalCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJournalCategoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JournalCategory  $journalCategory
     * @return \Illuminate\Http\Response
     */
    public function show(JournalCategory $journalCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JournalCategory  $journalCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(JournalCategory $journalCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateJournalCategoryRequest  $request
     * @param  \App\Models\JournalCategory  $journalCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJournalCategoryRequest $request, JournalCategory $journalCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JournalCategory  $journalCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(JournalCategory $journalCategory)
    {
        //
    }
}
