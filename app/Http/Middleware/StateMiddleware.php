<?php

namespace App\Http\Middleware;

use App\Models\JobsVacancy;
use App\Objects\States\States;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateMiddleware
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
        $state = $request->state;

        if ((new States())->hasChange($state) === false) {
            return response()->json([
                'errors' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => __('errors.action_is_prohibited'),
                ],
            ], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
