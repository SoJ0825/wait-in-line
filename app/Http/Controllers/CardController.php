<?php

namespace App\Http\Controllers;

use App\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function show()
    {
        Card::firstOrCreate(['id' => 1]);

        $card = Card::find(1);

        return ['result' => 'success', 'current' => $card->current];
    }
}
