<?php

namespace App\Http\Controllers;

use App\Desk;
use App\Card;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function show()
    {
        Card::firstOrCreate(['id' => 1]);

        $card = Card::find(1);

        return ['result' => 'success', 'current' => $card->current, 'serving' => Desk::servingCard()];
    }

    public function showUser(User $user)
    {
        return ['result' => 'success', 'card' => $user->card];
    }

    public function store(User $user)
    {
        Card::firstOrCreate(['id' => 1]);

        $card = Card::find(1);

        if ($user->isBeingServed()) {
            return response(['result' => 'fail', 'message' => 'Can\'t draw a card while being served']);
        }

        $card->sendTo($user);

        return ['result' => 'success', 'card' => $user->card];
    }

    public function destroy()
    {
        if (! Auth::user()->isAdmin()) {
            return response(['result' => 'fail'], 401);
        }

        Card::firstOrCreate(['id' => 1]);

        $card = Card::find(1);

        $card->resetCurrent();

        return ['result' => 'success'];
    }

    public function update()
    {
        if (! Auth::user()->isAdmin()) {
            return response(['result' => 'fail'], 401);
        }

        Card::firstOrCreate(['id' => 1]);

        $card = Card::find(1);

        $card->skip();

        return ['result' => 'success', 'current' => $card->current];
    }
}
