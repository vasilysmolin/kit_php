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
        $profile = $currentUser->profile;
        $resume = $request->route('resume');

        if (isset($resume)) {
            $vacancy = JobsResume::find($resume);
            if (isset($vacancy) && isset($profile) && $vacancy->profile_id !== $profile->profile_id) {
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
