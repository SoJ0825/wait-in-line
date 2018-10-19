<?php

namespace App;

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
        'password', 'api_token',
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
        $head = static::whereNotNull('card')
            ->orderBy('card')->first();

        return $this->id == $head->id;
    }

    public function throwCardAWay()
    {
        $this->card = null;

        $this->save();
    }
}
