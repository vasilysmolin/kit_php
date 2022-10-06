<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRealtyFilterRequest;
use App\Http\Requests\UpdateRealtyFilterRequest;
use App\Models\RealtyFilter;

class RealtyFilterController extends Controller
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
     * @param  \App\Http\Requests\StoreRealtyFilterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRealtyFilterRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RealtyFilter  $realtyFilter
     * @return \Illuminate\Http\Response
     */
    public function show(RealtyFilter $realtyFilter)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RealtyFilter  $realtyFilter
     * @return \Illuminate\Http\Response
     */
    public function edit(RealtyFilter $realtyFilter)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRealtyFilterRequest  $request
     * @param  \App\Models\RealtyFilter  $realtyFilter
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRealtyFilterRequest $request, RealtyFilter $realtyFilter)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RealtyFilter  $realtyFilter
     * @return \Illuminate\Http\Response
     */
    public function destroy(RealtyFilter $realtyFilter)
    {
    }
}
