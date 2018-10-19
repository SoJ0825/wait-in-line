<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    protected $fillable = ['user_id'];

    protected $hidden = ['created_at', 'updated_at'];

    public function serveCustomer(User $user)
    {
        $user->card = null;

        $user->save();

        $this->user_id = $user->id;

        $this->save();
    }

    public static function findEmpty()
    {
        return static::where('user_id', null)->first();
    }

    public function isServing()
    {
        return $this->user_id !== null;
    }

    public function leaveCustomer()
    {
        $this->user_id = null;

        $this->save();
    }
}
