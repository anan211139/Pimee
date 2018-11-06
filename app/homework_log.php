<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class homework_log extends Model
{
    protected $fillable = ['line_code', 'group_hw_id', 'exam_id','answer','is_correct'];
}
