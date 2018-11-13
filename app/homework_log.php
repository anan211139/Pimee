<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class homework_log extends Model
{
    protected $fillable = ['group_hw_id','send_groups_id', 'exam_id','answer','is_correct'];
}
