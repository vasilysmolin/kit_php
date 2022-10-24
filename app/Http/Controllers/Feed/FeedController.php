<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedRequest;
use App\Http\Requests\UpdateFeedRequest;
use App\Models\Feed;

class FeedController extends Controller
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


    public function store(StoreFeedRequest $request)
    {
        $feed = new Feed();
        $data = $request->all();
        $account = $request->get('accounts');
        $data['profile_id'] = $account['profile_id'];
        $feed->fill($data);
        $feed->save();
        return response()->json([], 201, ['Location' => "/feeds/$feed->id"]);
    }


    public function show(Feed $feed)
    {
        return response()->json($feed);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function edit(Feed $feed)
    {
    }


    public function update(UpdateFeedRequest $request, Feed $feed)
    {
        $data = $request->all();
        $account = $request->get('accounts');
        $data['profile_id'] = $account['profile_id'];
        $feed->fill($data);
        $feed->update();
        return response()->json([], 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feed $feed)
    {
    }
}
