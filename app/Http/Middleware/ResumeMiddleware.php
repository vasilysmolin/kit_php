<?php

namespace App\Http\Middleware;

use App\Models\JobsResume;
use App\Models\JobsVacancy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResumeMiddleware
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
        $resume = $request->route('resume');

        if (isset($resume)) {
            $resume = JobsResume::where('alias', $resume)
                ->when(ctype_digit($resume), function ($q) use ($resume) {
                    $q->orWhere('id', (int) $resume);
                })->withTrashed()
                ->first();
            if (isset($resume) && isset($profile) && $resume->profile_id !== $profile) {
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
