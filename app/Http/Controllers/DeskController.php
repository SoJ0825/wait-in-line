<?php

namespace App\Http\Controllers;

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
}
