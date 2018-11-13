<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class send_groups extends Model
{
    protected $fillable = ['examgroup_id', 'room_id', 'exp_date','noti_status','noti_date','key_status','key_date'];
}
