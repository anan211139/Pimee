<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam_new extends Model
{
    protected $fillable = ['chapter_id', 'level_id', 'question','choice_a','choice_b','choice_c','choice_d','local_pic', 'answer','parent_id', 'principle_id'];
}
