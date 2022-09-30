<?php

namespace App\Http\Middleware;

use App\Models\Realty;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RealtyMiddleware
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
        $realty = $request->route('realties');

        if (isset($realty)) {
            $realty = Realty::where('alias', $realty)
                ->when(ctype_digit($realty), function ($q) use ($realty) {
                    $q->orWhere('id', (int) $realty);
                })->withTrashed()
                ->first();
            if (isset($realty) && isset($profile) && $realty->profile_id !== (int) $profile) {
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
