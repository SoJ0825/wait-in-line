<?php

namespace App;

use App\Desk;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'email_verified_at', 'created_at', 'updated_at'
    ];

    public function generateApiToken()
    {
        do {
            $this->api_token = str_random(60);
        } while ($this->where('api_token', $this->api_token)->exists());

        $this->save();
    }

    public function isAdmin()
    {
        return in_array($this->name, ['ttn']);
    }

    public function isInLine()
    {
        return !! $this->card;
    }

    public function isHeadOfLine()
    {
        $servingCard = Desk::servingCard() ?? 0;
        $head = static::whereNotNull('card')
            ->where('card', '>', $servingCard)
            ->orderBy('card')->first();

        if ($head == null) {
            return false;
        }

        return $this->id == $head->id;
    }

    public function isOver()
    {
        if (Desk::servingCard() == null) {
            return false;
        }

        $behind = static::whereNotNull('card')
            ->where('card', '<=', Desk::servingCard())
            ->get()
            ->map(function ($user) {
                return $user->id;
            })
            ->toArray();

        return in_array($this->id, $behind);
    }

    public function throwCardAWay()
    {
        $this->card = null;

        $this->save();
    }

    public function isBeingServed()
    {
        return Desk::where('user_id', $this->id)->exists();
    }
}
