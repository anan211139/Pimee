<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['line_code', 'name', 'local_pic', 'point', 'birthofdate', 'email', 'phone', 'address', 'subject_id','chapter_id','hw_group_id','seq'];
}
