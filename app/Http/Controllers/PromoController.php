<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function promo(Request $request) {
        if ($request->isMethod('GET')) {
            return view('pages.promo');
        }
    }
}
