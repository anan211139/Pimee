<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class exam_test_group extends Model
{
    protected $fillable = ['line_code','send_groups_id','examgroup_id', 'status'];
}
