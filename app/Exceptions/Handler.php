<?php

namespace App\Exceptions;

use ErrorException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }


    public function render($request, Throwable $exception)
    {

        /**
         * Если отвалиться APi моего склада, чтобы не упал дашборд в Crm
         * */

        // лавливаем исключения guzzle
        if ($exception instanceof ConnectionException) {
            return response()->json([
            ], Response::HTTP_OK);
        }

        if ($exception && $request->is('api/*')) {
//            Log::debug($request->headers->all());


            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'errors' => [
                        'code' => 401 ,
                        'message' => 'не авторизован',
                    ],
                ], 401);
            }

            /**
             * Отаказано в доступе, не хватает прав
             * */
            if ($exception instanceof UnauthorizedException || $exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                return response()->json([
                    'errors' => [
                        'code' => Response::HTTP_FORBIDDEN,
                        'message' => __('errors.user_have_not_permission'),
                    ],
                ], Response::HTTP_FORBIDDEN);
            }

            /**
             * Просрочен токен авторизации
             * */


            if ($exception instanceof TokenExpiredException) {
                return response()->json([
                    'errors' => [
                        'code' => 100 ,
                        'message' =>  __('errors.user_have_not_token'),
                    ],
                ], Response::HTTP_UNAUTHORIZED);
            } elseif ($exception instanceof TokenInvalidException) {
                return response()->json([
                    'errors' => [
                        'code' => 101 ,
                        'message' => __('errors.user_have_not_token_failed'),
                    ],
                ], Response::HTTP_UNAUTHORIZED);
            } elseif ($exception instanceof TokenBlacklistedException) {
                return response()->json([
                    'errors' => [
                        'code' => 102 ,
                        'message' => __('errors.user_have_not_token_black_list'),
                    ],
                ], Response::HTTP_UNAUTHORIZED);
            }
            if ($exception->getMessage() === 'Token not provided') {
                return response()->json(
                    [
                        'errors' => [
                            'code' => 103 ,
                            'message' => __('errors.user_have_not_token'),
                        ],
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }
            if ($exception->getMessage() === 'Token has expired') {
                return response()->json(
                    [
                        'errors' => [
                            'code' => 100 ,
                            'message' => __('errors.user_have_not_token'),
                        ],
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }
            if ($exception->getMessage() === 'Wrong number of segments') {
                return response()->json(
                    [
                        'errors' => [
                            'code' => 104 ,
                            'message' => __('errors.user_have_not_token'),
                        ],
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }
            if ($exception->getMessage() === 'Token has expired') {
                return response()->json(
                    [
                        'errors' => [
                            'code' => 100 ,
                            'message' => __('errors.user_have_not_token'),
                        ],
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }
            if ($exception->getMessage() === 'Wrong number of segments') {
                return response()->json(
                    [
                        'errors' => [
                            'code' => 104 ,
                            'message' => __('errors.user_have_not_token'),
                        ],
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }


            return response()->json(
                [
                    'errors' => [
                        'code' => 500,
                        'message' => $exception->getMessage(),
                        'trace' => config('app.env') === 'production' ? '' : $exception->getTrace() ,
                        //                        'trace' => $exception->getTrace() ,
                    ],
                ], Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
