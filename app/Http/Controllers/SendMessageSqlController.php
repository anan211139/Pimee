<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;
// ยังไม่ใช้

class SqlController extends Controller
{   
    public function homework(Request $request){    
        $id = $request->input('value');
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
                                <a class="btn_hw">ทำ</a>
                            </div>
                        </td>
                    </tr>';          
                }
                            
                                
            }
    
        return $output;
    }

}
