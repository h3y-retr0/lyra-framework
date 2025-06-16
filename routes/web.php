<?php

use App\Models\User;
use Lyra\Auth\Auth;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Route;

Auth::routes();

Route::get('/', function () {
    if (isGuest()) {
        return Response::text('Guest');
    }
    
    return Response::text(auth()->name);
});

Route::get('/form', fn () => view("form"));
Route::get('/user/{user}', fn (User $user) => json($user->toArray()));
Route::get('/route/{param}', fn(int $param) => json(["param" => $param]));


Route::get('/picture', function (Request $request) {
    $url = $request->file('picture')->store();
    return $url;
});
