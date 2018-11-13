<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class homework_result_new extends Model
{
    protected $fillable = ['line_code','send_groups_id', 'examgroup_id', 'total'];
}
