<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ]);

        if (Auth::guard('web')->attempt($request->only('email', 'password'))) {
            $user = User::where('email', $request->email)->first();
            $user->generateApiToken();

            return ['result' => 'success', 'api_token' => $user->api_token];
        }

        return ['result' => 'fail'];
    }
}
