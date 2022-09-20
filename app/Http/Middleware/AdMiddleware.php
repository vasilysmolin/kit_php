<?php

namespace App\Http\Middleware;

use App\Models\CatalogAd;
use App\Models\JobsResume;
use App\Models\JobsVacancy;
use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUser = $request->user('api');
        if ($currentUser->hasRole('admin')) {
            return $next($request);
        }
        $profile = $request->get('accounts')['profile_id'];
        $ad = $request->route('declaration');

        if (isset($ad)) {
            $ad = CatalogAd::where('alias', $ad)
                ->when(ctype_digit($ad), function ($q) use ($ad) {
                    $q->orWhere('id', (int) $ad);
                })->withTrashed()
                ->first();
            if (isset($ad) && isset($profile) && $ad->profile_id !== (int) $profile) {
                return response()->json([
                    'errors' => [
                        'code' => Response::HTTP_FORBIDDEN,
                        'message' => __('errors.action_is_prohibited'),
                    ],
                ], Response::HTTP_FORBIDDEN);
            }
        }
        return $next($request);
    }
}
