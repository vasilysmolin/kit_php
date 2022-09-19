<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->user()->hasVerifiedEmail()) {
            $hash = encrypt([
                'email' => $request->user()->email,
                'date' => Carbon::now()->format('Y-m-d'),
            ]);
            Mail::to($request->user()->email)->queue(new EmailVerification($hash));
        }
        return response()->json([], 201);
    }
}
