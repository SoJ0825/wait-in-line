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

    public function skip($count = 1)
    {
        $this->increment('current', $count);
    }

    public function resetCurrent()
    {
        $this->current = 1;

        $this->save();

        $virtualDesk = Desk::find(6);

        if ($virtualDesk) {
            $virtualDesk->serving_card = null;
            $virtualDesk->save();
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->throwCardAWay();
        }
    }
}
