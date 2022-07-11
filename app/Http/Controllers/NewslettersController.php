<?php

namespace App\Http\Controllers;

use App\Exports\ExportNewsLetters;
use App\Http\Requests\StoreNewslettersRequest;
use App\Http\Requests\UpdateNewslettersRequest;
use App\Models\Newsletters;
use App\Objects\JsonHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NewslettersController extends Controller
{

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $builder = Newsletters::orderBy('id', 'DESC');
        $letters = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->get();
        $count = $builder->count();

        $data = (new JsonHelper())->getIndexStructure(new Newsletters(), $letters, $count, (int) $skip);

        return response()->json($data);
    }

    public function download()
    {
        $letters = Newsletters::get();

        $collectionToLetters = new ExportNewsLetters($letters->filter(function ($value) {
            return true;
        }));

        Excel::store($collectionToLetters, "letters.csv", 'local');
        $file =  storage_path('app/public/letters.csv');
        return response()->download($file)->deleteFileAfterSend(true);
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
