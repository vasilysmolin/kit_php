<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function promo() {
        return view('pages.promo');
    }
}
