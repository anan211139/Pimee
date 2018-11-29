<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class classroom extends Model
{
    protected $fillable = ['classroom_code', 'name', 'parent_id'];
}
