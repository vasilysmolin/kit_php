<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedRequest;
use App\Http\Requests\UpdateFeedRequest;
use App\Models\Feed;
use App\Models\Profile;
use App\Models\Realty;
use App\Models\User;
use App\Objects\JsonHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $account = $request->get('accounts');
        $profileID = $account['profile_id'];
        $user = User::whereHas('profile', function ($q) use ($profileID) {
            $q->where('id', $profileID);
        })->first();
        $feeds = $user->profile->feeds;

        $data = (new JsonHelper())->getIndexStructure(new Feed(), $feeds, $feeds->count(), 0);

        return response()->json($data);
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
        $account = $request->get('accounts');
        $profile = Profile::find($account['profile_id']);
        $hasFeeds = $profile->feeds->first();
        if ($hasFeeds) {
            return response()->json([],422);
        }
        $feed = new Feed();
        $data = $request->all();
        $data['profile_id'] = $account['profile_id'];
        $feed->fill($data);
        $feed->save();
        return response()->json(['id' => $feed->id], 201, ['Location' => "/feeds/$feed->id"]);
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


    public function destroy(Request $request, Feed $feed)
    {
        $account = $request->get('accounts');
        $profileID = $account['profile_id'];
        DB::transaction(function () use ($profileID, $feed) {
            Realty::whereHas('profile', function ($q) use ($profileID) {
                $q->where('id', $profileID);
            })
            ->whereNotNull('external_id')
                ->withTrashed()
                ->forceDelete();
            $feed->delete();
        }, 3);
        return response()->json([], 204);
    }
}
