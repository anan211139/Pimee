<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chat_sequence extends Model
{
    protected $fillable = ['send','to','type_reply','detail'];
}
