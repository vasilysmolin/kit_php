<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $hash = decrypt($request->hash);

        if ($user->email === $hash['email'] && Carbon::now()->format('Y-m-d') === $hash['date']) {
            $request->user()->markEmailAsVerified();
        }

        return response()->json([]);
    }
}
