<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;


class SqlController extends Controller
{   

    public function leaderboard($id){    

        $top_students = DB::select(
					        DB::raw("SELECT `id`, `name`, `point`, `local_pic`, FIND_IN_SET( `point`, (SELECT GROUP_CONCAT( `point` ORDER BY `point` DESC ) FROM students )) AS rank FROM students ORDER BY rank")
					    );
        // $rank_ms = DB::select(
					   //      DB::raw("SELECT `id`, `name`, `point`, `local_pic`, FIND_IN_SET( `point`, (SELECT GROUP_CONCAT( `point` ORDER BY `point` DESC ) FROM students )) AS rank FROM students")

					   //  )
        //                 ->where('line_code', '=',$id)
        //                 ->orderBy('rank', 'desc')
        //                 ->get(); 
                    
        $rank_ms = DB::select(
                            DB::raw("SELECT `id`, `name`, `point`, `local_pic`, FIND_IN_SET( `point`, (SELECT GROUP_CONCAT( `point` ORDER BY `point` DESC ) FROM students )) AS rank FROM students where `line_code` = :userid ORDER BY rank DESC"),array('userid'=>$id)

                        );
  
        return View::make('leaderboard')->with('top_students',$top_students)->with('rank_ms',$rank_ms);
    }
    public function homework($id){    

        $homework_me = DB::table('send_groups') //การบ้านทั้งหมดของเด็กคนนั้น
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.id as send_group_id','send_groups.noti_date as noti_date','send_groups.key_date as key_date')
            ->where('line_code',$id)
            ->get();

        $exam_not_finish = DB::table('exam_test_groups') //ยังทำไม่เสร็จ
            ->select('send_groups_id','examgroup_id')
            ->where('line_code',$id)
            ->where('status',false)
            ->get();
        
        $do_homework = DB::table('exam_test_groups') //ยังทำไม่เสร็จ
            ->select('send_groups_id')
            ->where('line_code',$id)
            ->get();

        $do_homework_ar = $do_homework->unique('send_groups_id')->pluck('send_groups_id')->toArray();

        $homework_me_ar = $homework_me->unique('send_group_id')->pluck('send_group_id')->toArray();

        $hw_id_not_yet = collect();

        foreach ($homework_me_ar as $homework_me_ar) { //การบ้านที่ยังไม่เคยทำ
            $not_yet = 0;
            for($i=0;$i<count($do_homework_ar);$i++){
                if($homework_me_ar == $do_homework_ar[$i]){
                    $not_yet =1;
                }
            }
            if($not_yet == 0){
                $hw_id_not_yet->push($homework_me_ar);
            }
        }
        

        $dt_not_yet = collect();
        foreach ($hw_id_not_yet as $hw_id_not_yet) { //วน get ข้อมูลยังไม่เคยทำ
            $detail_not_yet = DB::table('send_groups') //การบ้านทั้งหมดของเด็กคนนั้น
                ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
                ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
                ->select('send_groups.id as id','examgroups.name as title_hw','send_groups.id as send_group_id','send_groups.exp_date as exp_date',
                    \DB::raw("(SELECT name FROM managers
                          WHERE examgroups.parent_id = managers.id
                        ) as parent_name")
                    )
                ->where('line_code',$id)
                ->where('send_groups.id',$hw_id_not_yet)
                ->get();
            $dt_not_yet->push($detail_not_yet);
        }
        $dt_not_yet->all();
        dd($dt_not_yet);
        

        // return View::make('homework')->with('top_students',$top_students)->with('rank_ms',$rank_ms);
        return View::make('homework');
    }
}
