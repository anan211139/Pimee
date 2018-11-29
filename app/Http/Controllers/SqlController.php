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
    public function homework(Request $request){    
        // dd($request);
        // $id = 'U038940166356c6b9fb0dcf051aded27f';
        $id = $request->input('value');
        // dd($id);
        $mytime = Carbon::now();


        $homework_me = DB::table('send_groups') //การบ้านทั้งหมดของเด็กคนนั้น
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            // ->leftJoin('homework_result_news','homework_result_news.send_groups_id','=','send_groups.id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.id as send_group_id','send_groups.noti_date as noti_date'
                ,'send_groups.exp_date as exp_date','examgroups.id as exam_id',\DB::raw("(SELECT name FROM managers WHERE examgroups.parent_id = managers.id) as parent_name")
                ,\DB::raw("(SELECT total FROM homework_result_news WHERE homework_result_news.send_groups_id = send_groups.id AND homework_result_news.line_code = info_classrooms.line_code)as total")
                ,\DB::raw("(SELECT status FROM exam_test_groups WHERE exam_test_groups.send_groups_id = send_groups.id AND exam_test_groups.line_code = info_classrooms.line_code)as finish_status")
            )
            ->where('info_classrooms.line_code',$id)
            ->get();
            // dd($homework_me);
        $de_homewrk = json_decode($homework_me,true);
        $output ="";
        foreach($de_homewrk as $homework_notyet){
            $homework_notyet['exp_date'] =  date("d/m/Y", strtotime($homework_notyet['exp_date']));
            if($homework_notyet['finish_status'] === null){
                    $output .=                 
                    '<tr>
                        <td>'.$homework_notyet['title_hw'].'</td> 
                        <td class="date">'.$homework_notyet['parent_name'].'</td>
                        <td class="date">'.$homework_notyet['exp_date'].'</td>
                        <td>
                            <div class="do_hw">
                                <div class="btn_hw">ทำ</div>
                            </div>
                        </td>
                    </tr>';          
                }
                            
                                
            }
    
        return $output;
    }
    public function homework2(Request $request){
        // dd($request);
        // $id = 'U038940166356c6b9fb0dcf051aded27f';
        $id = $request->input('value');
        // dd($id);
        $mytime = Carbon::now();


        $homework_me = DB::table('send_groups') //การบ้านทั้งหมดของเด็กคนนั้น
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            // ->leftJoin('homework_result_news','homework_result_news.send_groups_id','=','send_groups.id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.id as send_group_id','send_groups.noti_date as noti_date'
                ,'send_groups.exp_date as exp_date','examgroups.id as exam_id',\DB::raw("(SELECT name FROM managers WHERE examgroups.parent_id = managers.id) as parent_name")
                ,\DB::raw("(SELECT total FROM homework_result_news WHERE homework_result_news.send_groups_id = send_groups.id AND homework_result_news.line_code = info_classrooms.line_code)as total")
                ,\DB::raw("(SELECT status FROM exam_test_groups WHERE exam_test_groups.send_groups_id = send_groups.id AND exam_test_groups.line_code = info_classrooms.line_code)as finish_status")
            )
            ->where('info_classrooms.line_code',$id)
            ->get();
            // dd($homework_me);
        $de_homewrk = json_decode($homework_me,true);
        $output ="";
        foreach($de_homewrk as $homework_finish){
            if($homework_finish['finish_status'] == 1){
                    $output .=     
                        '<tr>
                            <td>'.$homework_finish['title_hw'].'</td> 
                            <td class="date">'.$homework_finish['parent_name'].'</td>
                            <td class="date">'.$homework_finish['total'].'</td>
                          
                        </tr>';
                }
            }
        return $output;
    }
    public function callhomeworkpage(){
        return view('homework');
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
