<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        $email = $request->email;

        $user = Password::getUser($request->only('email'));
        $token = Password::createToken(
            $user
        );
        Mail::to($email)->queue(new ResetPassword($token, $email));

        return response()->json([]);
    }
}
