<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['id', 'current'];

    public $timestamps = false;

    public function sendTo(User $user)
    {
        $user->card = $this->current;

        $user->save();

        $this->increment('current');
    }

    public function resetCurrent()
    {
        $this->current = 1;

        $this->save();

        $users = User::all();

        foreach ($users as $user) {
            $user->throwCardAWay();
        }
    }
}
