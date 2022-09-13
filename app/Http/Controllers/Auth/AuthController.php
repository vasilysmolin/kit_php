<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request)
    {
        $email = Str::lower($request->email);
        $credentials = [
            'email' => $email,
            'password' => $request->password,
        ];
        $token = auth('api')->attempt($credentials);
        if ($token === false) {
            return response()->json(['errors' => [
                'code' => 422,
                'message' => __('validation.login'),
                ],
            ], 422);
        }

        respondWithToken($token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginHash()
    {
        $crypt = request(['hash']);
        if (isset($crypt['hash'])) {
            try {
                $crypt = Crypt::decryptString($crypt['hash']);
            } catch (DecryptException $e) {
                return response()->json(['errors' => [
                    'code' => 422,
                    'message' => __('validation.login'),
                ],
                ], 422);
            }

            $arr = explode(':', $crypt);

            if (count($arr) === 2) {
                $user = User::where('id', $arr[0])->where('email', $arr[1])->first();
                if (isset($user)) {
                    $password = $user->password;
                    $user->password = '$2y$10$8QAWs8PGKE.FJwixKl.gfeWkSz2izS9DJUgFNx5NuWkrQTlmWTrkC';
                    $user->update();
                    $token = auth('api')->attempt(['email' => $arr[1], 'password' => '1234567']);
                    $user->password = $password;
                    $user->update();

                    if ($token === false) {
                        return response()->json(['errors' => [
                            'code' => 422,
                            'message' => __('validation.login'),
                        ],
                        ], 422);
                    }

                    respondWithToken($token);
                }
            }
        }

        return response()->json(['errors' => [
            'code' => 422,
            'message' => __('validation.login'),
        ],
        ], 422);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRequest $request)
    {
        $email = Str::lower($request->email);
        $credentials = [
            'email' => $email,
            'password' => $request->password,
        ];

        $model = User::where('email', $email)->first();


        if (!isset($model)) {
            $user = new User();
            $user->email = $email;
            $user->password = bcrypt(request('password'));
            $user->save();
            $user->profile()->create();
            if (isset($request->is_person) && !empty($request->is_person)) {
                $user->profile->isPerson = true;
                $user->profile->update();
            }
            if (isset($request->inn) && !empty($request->inn)) {
                $user->profile->isPerson = true;
                $user->profile->update();
                $user->profile->person()->create([
                    'inn' => $request->inn,
                    'name' => $request->inn,
                ]);
            }
            $token = auth('api')->attempt($credentials);

            if ($token === false) {
                return response()->json(['errors' => [
                    'code' => 422,
                    'message' => __('validation.login'),
                    ],
                ], 422);
            }

            respondWithToken($token);
        } else {
            return response()->json(['errors' => [
                'code' => 422,
                'message' => 'Пользователь уже существует',
            ],
            ], 422);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        $user = auth('api')->user();
        if (!isset($user->profile)) {
            $user->profile()->create();
        }
        $user->setAttribute('role', $user->getRoleNames()->first());
        return response()->json($user->load(['profile.restaurant', 'profile.person','city']));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        respondWithToken(auth('api')->refresh());
    }
}
