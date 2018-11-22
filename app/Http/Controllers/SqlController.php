<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
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

        $mytime = Carbon::now();
        // echo $mytime->toDateTimeString();

        $dt_not_yet = collect();
        $hw_id_not_yet = collect();

        $dt_finish = collect();
        $hw_id_finish = collect();

        $homework_me = DB::table('send_groups') //การบ้านทั้งหมดของเด็กคนนั้น
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.id as send_group_id','send_groups.noti_date as noti_date','send_groups.key_date as key_date')
            ->where('line_code',$id)
            // ->where('send_groups.exp_date','>=',$mytime )
            ->get();
        // dd($homework_me);
        $exam_not_finish = DB::table('exam_test_groups') //ยังทำไม่เสร็จ
            // ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->select('send_groups_id')
            ->where('line_code',$id)
            ->where('status',false)
            // ->where('send_groups.exp_date','>=',$mytime )
            ->get();
        // dd($exam_not_finish);
        $exam_finish = DB::table('exam_test_groups') //ทำเสร็จแล้ว
            ->select('send_groups_id')
            ->where('line_code',$id)
            ->where('status',true)
            ->get();     
        //dd($exam_finish);   

        $hw_not_finish = $exam_not_finish->unique('send_groups_id')->pluck('send_groups_id')->toArray();
        $hw_finish = $exam_finish->unique('send_groups_id')->pluck('send_groups_id')->toArray();
        // dd($hw_not_finish);
        $hw_id_not_yet->push($hw_not_finish);
        $hw_id_finish->push($hw_finish);
        // dd( $hw_id_finish);


        $do_homework = DB::table('exam_test_groups') //ยังทำไม่เสร็จ
            ->select('send_groups_id')
            ->where('line_code',$id)
            ->get();

        $do_homework_ar = $do_homework->unique('send_groups_id')->pluck('send_groups_id')->toArray();

        $homework_me_ar = $homework_me->unique('send_group_id')->pluck('send_group_id')->toArray();
        // dd($homework_me_ar);

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
        // dd($hw_id_not_yet);
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

        foreach ($hw_id_finish as $hw_id_finish) { //วน get ข้อมูลทำเสร็จแล้ว
            // echo $hw_id_finish.",";
            $detail_finish = DB::table('send_groups') //การบ้านทั้งหมดของเด็กคนนั้น
                ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
                ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
                ->select('send_groups.id as id','examgroups.name as title_hw','send_groups.id as send_group_id','send_groups.exp_date as exp_date',
                    \DB::raw("(SELECT name FROM managers
                          WHERE examgroups.parent_id = managers.id
                        ) as parent_name"),
                    \DB::raw("(SELECT count(id) FROM info_examgroups
                          WHERE info_examgroups.examgroup_id = send_groups.examgroup_id
                        ) as max_point"),
                    \DB::raw("(SELECT total FROM homework_result_news
                          WHERE homework_result_news.examgroup_id = send_groups.examgroup_id AND homework_result_news.send_groups_id = send_groups.id AND homework_result_news.line_code = info_classrooms.line_code
                        ) as total_point")
                    )
                ->where('line_code',$id)
                ->where('send_groups.id',$hw_id_finish)
                ->get();
            $dt_finish->push($detail_finish);
        }
        $dt_finish->all();
        // dd($dt_finish); //ข้อมูลแสดงใน web ของการบ้านที่ยังไม่ได้ทำ & ทำยังไม่เสร็จ


        return View::make('homework')->with('dt_not_yet',$dt_not_yet)->with('dt_finish',$dt_finish);
    }
    public function detail_homework($id,$send_group_id){ 
        // echo $id;
        $examgroup_id = DB::table('send_groups')
            ->select('examgroup_id')
            ->where('id',$send_group_id)
            ->first();
        // dd($examgroup_id);

        $exam_log = DB::table('info_examgroups')
            ->join('homework_logs','info_examgroups.exam_id','=','homework_logs.exam_id')
            ->select('info_examgroups.exam_id as exam_id','homework_logs.answer as answer','homework_logs.is_correct as is_correct')
            ->where('homework_logs.send_groups_id',$send_group_id)
            ->where('info_examgroups.examgroup_id',$examgroup_id->examgroup_id)
            ->where('homework_logs.line_code',$id)
            ->get();

        // dd($exam_log);
        $exam_topic = DB::table('examgroups')
            ->join('managers','managers.id','=','examgroups.parent_id')
            ->select('examgroups.name as subject','managers.name as name_parent')
            ->where('examgroups.id',$examgroup_id->examgroup_id)
            ->get();
        // dd($exam_topic);

        

        // dd($exam_id);
        $count_quiz = 1;
        $count_true = 0;
        $result = array();
        // echo $count_quiz ;        
        // dd($exam_log);
        foreach($exam_log as $exam_log){
            
            if($exam_log->is_correct == 0){
                $exam_detail = DB::table('exam_news')
                    ->select('question','choice_a','choice_b','choice_c','choice_d','local_pic','answer as true_answer','principle_id')
                    ->where('id',$exam_log->exam_id)
                    ->first();
         
                $principle_detail = DB::table('principle_news')
                    ->select('local_pic','detail')
                    ->where('id',$exam_detail->principle_id)
                    ->first();
              
                $result[$count_true]['count_quiz'] = $count_quiz;
                $result[$count_true]['local_pic_exam'] = $exam_detail->local_pic;
                $result[$count_true]['exam'] = $exam_detail->question;
                $result[$count_true]['choice_a'] = $exam_detail->choice_a;
                $result[$count_true]['choice_b'] = $exam_detail->choice_b;
                $result[$count_true]['choice_c'] = $exam_detail->choice_c;
                $result[$count_true]['choice_d'] = $exam_detail->choice_d;
                $result[$count_true]['true_ans'] = $exam_detail->true_answer;
                $result[$count_true]['ans_selec'] = $exam_log->answer;
                $result[$count_true]['local_pic_princ'] = $principle_detail->local_pic;
                $result[$count_true]['princ_detail'] = $principle_detail->detail;

            }
            
            $count_quiz++;
            $count_true++;
        }
         // dd($result);
        return View::make('detail_homework')->with('result',$result)->with('exam_topic',$exam_topic);
    }
}
