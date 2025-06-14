<?php

namespace App\Controllers\Auth;

use App\Models\User;
use Lyra\Crypto\Hasher;
use Lyra\Http\Controller;
use Lyra\Http\Request;

class RegisterController extends Controller {
    public function create() {
        return view('auth/register');
    }

    public function store(Request $request, Hasher $hasher) {
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

        $data["password"] = $hasher->hash($data["password"]);

        $user = User::create($data);

        $user->login();

        return redirect('/');
    }
}
