<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAccountMiddleware
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
        if (isset($currentUser)) {
            $token = JWTAuth::getToken();
            if (!empty($token)) {
                $claims = JWTAuth::parseToken()->getPayload()->getClaims();
                if (!empty($claims['profile_id'])) {
                    $request->request->add(['accounts' => [
                        'profile_id' => $claims['profile_id']->getValue(),
                        'id' => $claims['id']->getValue(),
                    ],
                    ]);
                } else {
                    $request->request->add(['accounts' => [
                        'profile_id' => $currentUser->profile->getKey(),
                        'id' => $currentUser->getKey(),
                    ],
                    ]);
                }
            }
        }

        return $next($request);
    }
}
