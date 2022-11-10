<?php

namespace App\Services;

use App\Jobs\RealtyImportJob;
use App\Models\Feed;
use App\Models\Profile;
use GuzzleHttp\Client;

class ImportFeedService
{

    public function import(Feed $feed, Profile $profile, bool $isSync = false): void
    {
        $client = new Client();
        $content = $client->get($feed->url, [
            'verify' => false,
        ]);
        $realties = new SimpleXMLElement($content->getBody()->getContents());;
        $realtiesExternal = $profile->realties()
            ->select(['id', 'external_id'])
            ->whereNotNull('external_id')
            ->get();
        $collect = collect([]);
        if ($feed->type === 'cian') {
            foreach ($realties->object as $realty) {
                $collect->add($realty->asXML());
            }
        }
        if ($feed->type === 'yandex') {
            foreach ($realties->offer as $realty) {
                $collect->add($realty->asXML());
            }
        }

        if ($feed->type === 'avito') {
            foreach ($realties->Ad as $realty) {
                $collect->add($realty->asXML());
            }
        }

        $chunkCollect = $collect->chunk(5);

        foreach ($chunkCollect as $realty) {
            if($isSync) {
                RealtyImportJob::dispatchSync($realty,$realtiesExternal,$profile, $feed->type);
            } else {
                RealtyImportJob::dispatch($realty,$realtiesExternal,$profile, $feed->type);
            }
        }
    }

}
