<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Realty;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Http\Request;

class JournalController extends Controller
{

    public function index(Request $request)
    {
        $take = $request->take ?? config('settings.take_twenty_five');
        $user = auth('api')->user();
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $files = resolve(Files::class);
        $catalog = $request->from === 'catalog';
        $cabinet = isset($user) && $request->from === 'cabinet';
        $querySearch = $request->querySearch;
        $account = $request->get('accounts');

        $builder = Journal::search($querySearch, function ($meilisearch, $query, $options) use ($skip, $account, $cabinet) {
            if (!empty($skip)) {
                $options['offset'] = (int) $skip;
            }
            $filters = [];
            if ($cabinet) {
                $filters[] = "profile_id = '{$account['profile_id']}'";
            }

            $options['filter'] = $filters;
            $options['sort'][] = "id:desc";
            return $meilisearch->search($query, $options);
        });
        $journalsBuilder = $builder->paginateRaw($take, 'page', $skip / $take + 1);

        $journals = $journalsBuilder->items() ? $journalsBuilder->items()['hits'] : [];

        $data = (new JsonHelper())->getIndexStructure(new Journal(), $journals, $journalsBuilder->total(), (int) $skip);

        return response()->json($data);
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
     * @param  \App\Http\Requests\StoreJournalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJournalRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\Http\Response
     */
    public function show(Journal $journal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\Http\Response
     */
    public function edit(Journal $journal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateJournalRequest  $request
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Journal $journal)
    {
        //
    }
}
