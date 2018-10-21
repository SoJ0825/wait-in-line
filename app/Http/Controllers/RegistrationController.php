<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function index()
    {
        if (! Auth::user()->isAdmin()) {
            return response(['result' => 'fail'], 401);
        }

        $users = User::all()->filter(function ($user) {
            if (! $user->isAdmin()) {
                return $user;
            }
        });

        return ['result' => 'success', 'users' => $users];
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|max:255'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return ['result' => 'success'];
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'current_password' => 'required|max:255',
            'update_password' => 'required|max:255'
        ]);

        $user = Auth::user();
        if (! Hash::check($request->current_password, $user->password)) {
            return ['result' => 'fail', 'message' => 'Check your credentials'];
        }

        $user->update([
            'name' => $request->name,
            'password' => bcrypt($request->update_password),
        ]);

        return ['result' => 'success'];
    }
}
