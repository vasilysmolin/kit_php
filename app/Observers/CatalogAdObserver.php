<?php

namespace App\Observers;

use App\Events\StateEmailEvent;
use App\Models\CatalogAd;

class CatalogAdObserver
{
    /**
     * Handle the CatalogAd "created" event.
     *
     * @param  \App\Models\CatalogAd  $catalogAd
     * @return void
     */
    public function created(CatalogAd $catalogAd)
    {
    }

    /**
     * Handle the CatalogAd "updated" event.
     *
     * @param  \App\Models\CatalogAd  $catalogAd
     * @return void
     */
    public function updated(CatalogAd $catalogAd)
    {
        if ($catalogAd->state !== $catalogAd->getOriginal('state')) {
            event(new StateEmailEvent($catalogAd, $catalogAd->profile->user->email));
        }
    }

    /**
     * Handle the CatalogAd "deleted" event.
     *
     * @param  \App\Models\CatalogAd  $catalogAd
     * @return void
     */
    public function deleted(CatalogAd $catalogAd)
    {
    }

    /**
     * Handle the CatalogAd "restored" event.
     *
     * @param  \App\Models\CatalogAd  $catalogAd
     * @return void
     */
    public function restored(CatalogAd $catalogAd)
    {
    }

    /**
     * Handle the CatalogAd "force deleted" event.
     *
     * @param  \App\Models\CatalogAd  $catalogAd
     * @return void
     */
    public function forceDeleted(CatalogAd $catalogAd)
    {
    }
}
