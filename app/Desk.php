<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    protected $fillable = ['user_id'];

    protected $hidden = ['created_at', 'updated_at'];
}
