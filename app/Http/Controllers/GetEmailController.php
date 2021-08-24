<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GetEmailRequest;
use App\Models\GetEmail;

class GetEmailController extends Controller
{
    public function email(GetEmailRequest $request) {
        $getEmail = new GetEmail();
        $getEmail->email = $request->input('email');
        $getEmail->save();

        return redirect()->route('email-success');
    }
}
