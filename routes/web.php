<?php

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Models\User;
use Lyra\Auth\Auth;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Route;

Auth::routes();

Route::get('/', fn () => redirect('/home'));

Route::get('/home', [HomeController::class, 'show']);
