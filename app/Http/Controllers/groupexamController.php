<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Info_examgroups;
use App\send_groups;
use App\Examgroups;

class groupexamController extends Controller
{
    public function addtogroup(Request $request){
        $exam_id = $request->input('exam_id');
        $parent_id = session('id','default');
        $jsongroup = DB::table('examgroups')->where('parent_id','=',$parent_id)->get();
        $group = json_decode($jsongroup,TRUE);
        $input = $request->input('group_id');
        foreach($input as $obj){
                $insert = new Info_examgroups;
                $insert->exam_id = $exam_id;
                $insert->examgroup_id = $obj;
                $insert->save();
        }
        return redirect('/aboutexam');
        // return "Done";
    }
    public function sendexamtoroom(Request $request){
        $group_id = $request->input('groupexam');
        $arrayroom = $request->input('room_id');
        $exp = $request->input('exp');
        $noti = $request->input('noti');
        $key = $request->input('key');
        // dd($exp,$noti,$key);
        foreach($arrayroom as $obj){
            $insert = new send_groups;
            $insert->examgroup_id = $group_id;
            $insert->room_id = $obj;
            $insert->exp_date = $exp;
            $insert->noti_date = $noti;
            $insert->key_date = $key;
            $insert->save();
        }
        // dd($arrayroom);
        return redirect('/aboutexam');
    }
    public function newgroup(Request $request){
        $name = $request->input('name');
        $id = session('id','default');
        $insert = new Examgroups;
        $insert->name = $name;
        $insert->parent_id = $id;
        $insert->save();
        return redirect('/aboutexam');
    }
}
