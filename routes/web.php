<?php

use App\Models\User;
use Lyra\Crypto\Hasher;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Route;

Route::get('/', function ($request) {
    if (isGuest()) {
        return Response::text('Guest');
    }
    
    return Response::text(auth()->name);
});

Route::get('/form', fn ($request) => view("form"));

Route::get('/register', fn ($request) => view("auth/register"));
Route::post('/register', function (Request $request) {
    $data = $request->validate([
        "email" => ["required", "email"],
        "name" => "required",
        "password" => "required",
        "confirm_password" => "required",
    ]);

    if ($data["password"] !== $data["confirm_password"]) {
        return back()->withErrors([
            "confirm_password" => ["confirm_password" => "Passwords do not match"]]
        );
    }

    $data["password"] = app(Hasher::class)->hash($data["password"]);

    User::create($data);

    $user = User::firstWhere('email', $data['email']);

    $user->login();

    return redirect('/');
});

Route::get('/login', fn ($request) => view('auth/login'));

Route::post('/login', function (Request $request) {
    $data = $request->validate([
        "email" => ["required", "email"],
        "password" => "required",
    ]);

    $user = User::firstWhere('email', $data['email']);

    if (is_null($user) || !app(Hasher::class)->verify($data["password"], $user->password)) {
        return back()->withErrors([
            'email' => ['email' => 'Credentials are invalid'],
            'password' => ['password' => 'Credentials are invalid']
        ]);
    }

    $user->login();

    return redirect('/');
});

Route::get('/logout', function ($request) {
    auth()->logout();
    return redirect('/');
});

