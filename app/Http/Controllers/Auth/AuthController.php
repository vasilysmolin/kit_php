<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);


        $token = auth('api')->attempt($credentials);
        if ($token === false) {
            return response()->json(['errors' => [
                'code' => 422,
                'message' => 'Неверный логин или пароль',
                ],
            ], 422);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginHash()
    {
//        $str = '794:black_info@bk.ru';
//        $crypt = Crypt::encryptString($str);
        $crypt = request(['hash']);
        if (isset($crypt['hash'])) {
            try {
                $crypt = Crypt::decryptString($crypt['hash']);
            } catch (DecryptException $e) {
                return response()->json(['errors' => [
                    'code' => 422,
                    'message' => 'Неверный логин или пароль',
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
                            'message' => 'Неверный логин или пароль',
                        ],
                        ], 422);
                    }

                    return $this->respondWithToken($token);
                }
            }
        }

        return response()->json(['errors' => [
            'code' => 422,
            'message' => 'Неверный логин или пароль',
        ],
        ], 422);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        $credentials = request(['email', 'password']);

        $model = User::where('email', request('email'))->first();


        if (!isset($model)) {
            $user = new User();
            $user->email = request('email');
            $user->password = bcrypt(request('password'));
            $user->save();
            $token = auth('api')->attempt($credentials);

            if ($token === false) {
                return response()->json(['errors' => [
                    'code' => 422,
                    'message' => 'Неверный логин или пароль',
                    ],
                ], 422);
            }

            return $this->respondWithToken($token);
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
        $user->profile()->create();
        return response()->json($user->load('profile.restaurant'));
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
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
