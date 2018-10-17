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
}
