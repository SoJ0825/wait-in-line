<?php

namespace App\Http\Controllers;

use App\User;
use App\Desk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeskController extends Controller
{
    public function index()
    {
        if (! Auth::user()->isAdmin()) {
            return response(['result' => 'fail'], 401);
        }

        $desks = Desk::all();

        return ['result' => 'success', 'desks' => $desks];
    }

    public function store(User $user)
    {
        if (! Auth::user()->isAdmin()) {
            return response(['result' => 'fail'], 401);
        }

        if (! $user->isInLine()) {
            return response(['result' => 'fail', 'message' => 'Draw a card first']);
        }

        if (! $user->isHeadOfLine()) {
            return response(['result' => 'fail', 'message' => 'Not your turn']);
        }

        if (! $desk = Desk::findEmpty()) {
            return response(['result' => 'fail', 'message' => 'No empty desk']);
        }

        $desk->serveCustomer($user);

        return ['result' => 'success', 'desk' => $desk->id];
    }
}
