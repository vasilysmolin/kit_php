<?php

namespace App\Http\Middleware;

use App\Models\JobsResume;
use App\Models\JobsVacancy;
use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceMiddleware
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
        $profile = $currentUser->profile;
        $service = $request->route('service');

        if (isset($service)) {
            $service = Service::where('alias', $service)
                ->when(ctype_digit($service), function ($q) use ($service) {
                    $q->orWhere('id', (int) $service);
                })->withTrashed()
                ->first();
            if (isset($service) && isset($profile) && $service->profile_id !== $currentUser->profile->getKey()) {
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
