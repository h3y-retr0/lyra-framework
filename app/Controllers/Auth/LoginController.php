<?php

namespace App\Controllers\Auth;

use App\Models\User;
use Lyra\Crypto\Hasher;
use Lyra\Http\Controller;
use Lyra\Http\Request;

class LoginController extends Controller{
    public function create() {
        return view('auth/login');
    }

    public function store(Request $request, Hasher $hasher) {
        $data = $request->validate([
            "email" => ["required", "email"],
            "password" => "required",
        ]);

        $user = User::firstWhere('email', $data['email']);

        if (is_null($user) || !$hasher->verify($data["password"], $user->password)) {
            return back()->withErrors([
                'email' => ['email' => 'Credentials are invalid'],
                'password' => ['password' => 'Credentials are invalid']
            ]);
        }
        $user->login();
        return redirect('/');     
    }
    
    public function destroy() {
        auth()->logout();
        return redirect('/');
    }
}
