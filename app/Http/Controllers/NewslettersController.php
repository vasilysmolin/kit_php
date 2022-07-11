<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewslettersRequest;
use App\Http\Requests\UpdateNewslettersRequest;
use App\Models\Newsletters;

class NewslettersController extends Controller
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

    public function store(StoreNewslettersRequest $request): \Illuminate\Http\JsonResponse
    {
        $letters = new Newsletters();
        $letters->fill($request->all());
        $letters->save();
        return response()->json([], 201, ['Location' => "/newsletters/$letters->id"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Newsletters  $newsletters
     * @return \Illuminate\Http\Response
     */
    public function show(Newsletters $newsletters)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNewslettersRequest  $request
     * @param  \App\Models\Newsletters  $newsletters
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNewslettersRequest $request, Newsletters $newsletters)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Newsletters  $newsletters
     * @return \Illuminate\Http\Response
     */
    public function destroy(Newsletters $newsletters)
    {
        //
    }
}
