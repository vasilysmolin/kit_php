<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRealtyCategoryRequest;
use App\Http\Requests\UpdateRealtyCategoryRequest;
use App\Models\RealtyCategory;

class RealtyCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRealtyCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRealtyCategoryRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RealtyCategory  $realtyCategory
     * @return \Illuminate\Http\Response
     */
    public function show(RealtyCategory $realtyCategory)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RealtyCategory  $realtyCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(RealtyCategory $realtyCategory)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRealtyCategoryRequest  $request
     * @param  \App\Models\RealtyCategory  $realtyCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRealtyCategoryRequest $request, RealtyCategory $realtyCategory)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RealtyCategory  $realtyCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(RealtyCategory $realtyCategory)
    {
    }
}
