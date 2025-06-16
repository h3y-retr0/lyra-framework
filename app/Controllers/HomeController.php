<?php

namespace App\Controllers;

use Lyra\Http\Controller;

class HomeController extends Controller {
    public function show() {
        return view('home');
    }
}