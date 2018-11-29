<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Info_classroom;
use Illuminate\Support\Facades\DB;
use App\exam_new;
use App\Chapter;
use App\Feedbacks;

class Ajaxcontroller extends Controller
{
    public function sentconnectpageByajax(Request $request){
        $classroom_code = $request->input('roomcode');
        $line_code = $request->input('line_code');

        $query = DB::table('classrooms')
        ->where('classroom_code','=',$classroom_code)
        ->get();
        $arrayresult = json_decode($query,true); 
        $query_line_code =DB::table('students')
        ->where('line_code','=',$line_code)
        ->get();
        $arrayresult_line_code = json_decode($query_line_code,true); 

        if(count($arrayresult) && count($arrayresult_line_code)){
            $insert = new Info_classroom;
            $insert->line_code = $arrayresult_line_code[0]['line_code'];
            $insert->classroom_id = $arrayresult[0]['id'];
            $insert->save();
            echo $line_code;
        }
        else{
            echo "202";
            //new
        }
    }
    public function checkrommcode(Request $request){
        $code = $request->input('code');
        $query = DB::table('classrooms')
        ->where('classroom_code','=',$code)
        ->get();
        $arrayresult = json_decode($query,true); 
        if(count($arrayresult)){
            echo "1";
        }else{
            echo "0";
        }
    }
    public function sentstudataByajax(Request $request){
        $line_code = $request->input('line_code');
        $name = $request->input('name');
        $query_line_code =DB::table('students')
        ->where('line_code','=',$line_code)
        ->get();
        $arrayresult_line_code = json_decode($query_line_code,true); 

        if(count($arrayresult_line_code)){
            DB::table('students')
            ->where('line_code', $line_code)
            ->update(['name' => $name]);

            echo $line_code;
        }
        else{
            return "in else";
        }
    }
    public function admininsertexam(Request $request){
        $question = $request->input('question');
        $choice_a = $request->input('a');
        $choice_b = $request->input('b');
        $choice_c = $request->input('c');
        $choice_d = $request->input('d');
        $level = $request->input('level');

        $exam = new exam_new;
        $exam->question = $name;
        $exam->choice_a = $choice_a;
        $exam->choice_b = $choice_b;
        $exam->choice_c = $choice_c;
        $exam->choice_d = $choice_d;
        $exam->save();

        return '200';
    }
    public function selectexamlist(Request $request){
        $examowner = $request->input('owner');//ยังไม่ได้ทำ
        $chapter_id = $request->input('chapter');
        $subject_id = $request->input('subject');
        $level = $request->input('level');
        $jsonresult = DB::table('exam_news')
        ->leftjoin('chapters','exam_news.chapter_id','chapters.id')
        ->where('chapters.subject_id','=',$subject_id)
        ->where('chapters.id','=',$chapter_id)
        ->where('exam_news.level_id','=',$level)
        ->get();
        return '200';
    }
    public function selectexam(Request $request){
        $id =$request->input('id');
        return $id;
    }
    public function selectchapterAjax(Request $request){
        $subid = $request->input('subject');
        $output = '<option value="0">ทุกบท</option>';
        if($subid == 0){
            $allchapter = DB::table('chapters')->get();
            foreach($allchapter as $obj){
                $output .= '<option value="'.$obj->id.'">'.$obj->name.'</option>';
            }
            return $output;
        }
        $result = DB::table('chapters')->where('subject_id','=',$subid)->get();
        foreach($result as $obj){
            $output .= '<option value="'.$obj->id.'">'.$obj->name.'</option>';
        }
        return $output;
    }
    public function Ajaxquerygroupexam(Request $request){
        $id = $request->input('id');
        // DD($request);
        $jsonresult = DB::table('info_examgroups')
        ->leftjoin('exam_news','exam_id','=','exam_news.id')
        ->select('info_examgroups.exam_id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','info_examgroups.updated_at')
        ->where('examgroup_id','=',$id)
        ->get();
        // dd($jsonresult);
        $arrayresult = json_decode($jsonresult,TRUE);
        $output = "";
        $i = 1;
        foreach($arrayresult as $obj){
            $output .= "
            <div>
                <h3>ข้อ ".$i."</h3>
                <p>". $obj['question'] ."</p>
            </div>";
            $i++;
        }
        return $output;
    }
    public function updateexamlist(Request $request){
        $onwer = $request->input('onwer');
        $subject = $request->input('subject');
        $chapter = $request->input('chapter');
        $level = $request->input('level');
        $parent_id = session('id','default');
        if($onwer == 0){
            if($subject == 0){
                if($chapter == 0){
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('level_id','=',$level)
                        ->get();
                    }
                }else{
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('chapter_id','=',$chapter)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('chapter_id','=',$chapter)
                        ->where('level_id','=',$level)
                        ->get();
                    }
                }
            }else{
                if($chapter == 0){
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('level_id','=',$level)
                        ->get();
                    }
                }else{
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('chapter_id','=',$chapter)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('chapter_id','=',$chapter)
                        ->where('level_id','=',$level)
                        ->get();
                    }
                }  
            }
        }elseif($onwer == 1){
            if($subject == 0){
                if($chapter == 0){
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('level_id','=',$level)
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }
                }else{
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('chapter_id','=',$chapter)
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('chapter_id','=',$chapter)
                        ->where('level_id','=',$level)
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }
                }
            }else{
                if($chapter == 0){
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('level_id','=',$level)
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }
                }else{
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('chapter_id','=',$chapter)
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('chapter_id','=',$chapter)
                        ->where('level_id','=',$level)
                        ->where('parent_id','=',$parent_id)
                        ->get();
                    }
                }  
            }
        }elseif($onwer == 2){
            if($subject == 0){
                if($chapter == 0){
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('level_id','=',$level)
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }
                }else{
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('chapter_id','=',$chapter)
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->where('chapter_id','=',$chapter)
                        ->where('level_id','=',$level)
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }
                }
            }else{
                if($chapter == 0){
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('level_id','=',$level)
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }
                }else{
                    if($level == 0){
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('chapter_id','=',$chapter)
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }else{
                        $queryjson = DB::table('exam_news')
                        ->select('exam_news.id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','exam_news.updated_at')
                        ->leftjoin('chapters','chapter_id','=','chapters.id')
                        ->where('subject_id','=',$subject)
                        ->where('chapter_id','=',$chapter)
                        ->where('level_id','=',$level)
                        ->where('parent_id','<>',$parent_id)
                        ->get();
                    }
                }  
            }
        }
        $jsoncorrect = DB::table('logChildrenQuizzes')
        ->select(DB::raw('count(is_correct) as num,exam_id'))
        ->groupBy('exam_id')
        ->where('is_correct','=','1')
        ->get();
        $jsonwrong = DB::table('logChildrenQuizzes')
        ->select(DB::raw('count(is_correct) as num,exam_id'))
        ->groupBy('exam_id')
        ->where('is_correct','=','0')
        ->get();
        $correct = json_decode($jsoncorrect, true);
        $wrong = json_decode($jsonwrong, true);
        $exam = json_decode($queryjson, true);
        $output = "";
        $i = 1;
        foreach($exam as $obj){
            $temp = -1;
            $temp2 = -1;
            foreach($correct as $num){
                if($obj['id'] == $num['exam_id']){
                    $temp = $i - 1;
                }
            }foreach($wrong as $num){
                if($obj['id'] == $num['exam_id']){
                    $temp2 = $i - 1;
                }
            }
            if($temp == -1){
                $cor = 0;
            }else{
                $cor = $correct[$temp]['num'];
            }if($temp2 == -1){
                $wro = 0;
            }else{
                $wro = $wrong[$temp2]['num'];
            }
            $output .= "<tr role=\"row\" onclick=\"document.getElementById('exam" . $obj['id'] . "').style.display='block'\">" 
            . "<td role=\"cell\">" . $i . "</td>"
            . "<td role=\"cell\">" . substr($obj['question'], 0, 50) . "</td>"
            . "<td role=\"cell\">" . $cor . "</td>"
            . "<td role=\"cell\">" . $wro . "</td></tr>";
            $i++;
            
        }
        return $output;
        

    }
    public function Ajaxsendreport(Request $request){
        $exam_id = $request->input('exam_id');
        $report = $request->input('report');
        $parent_id = session('id','default');
        $head = "About exam";
        $insert = new Feedbacks;
        $insert->exam_id = $exam_id;
        $insert->details = $report;
        $insert->parent_id = $parent_id;
        $insert->typereport_id = 1;
        $insert->head = $head;
        $insert->save();
        return "Done";
    }
    public function queryhomeworkresult(Request $request){
        $group = $request->input('chapter');
        // return 'asd';
        $choosechild = session('choosechild','default');
        if($group == 0){
            $query = DB::table('homework_logs')
            ->leftjoin('exam_news','homework_logs.exam_id','=','exam_news.id')
            ->leftjoin('chapters','exam_news.chapter_id','chapters.id')
            ->select(DB::raw('homework_logs.exam_id,homework_logs.answer,homework_logs.is_correct,exam_news.question,exam_news.choice_a,exam_news.choice_b,exam_news.choice_c,exam_news.choice_d,exam_news.local_pic,exam_news.answer as solution,chapters.name as chapter_name'))
            ->where('line_code','=',$choosechild)
            ->get();
            $query = json_decode($query, TRUE);
            $output = "";
            $i = '1';
            foreach($query as $obj){
                if($obj['is_correct'] == 1){
                    $status = 'ถูก';
                }else{
                    $status = 'ผิด';
                }
                $output .= '<tr role="row" id = "'.$i.'" class = "exam-ele"  onclick="pop(this.id)">
                                <td role="cell">'. $i .'</td>
                                <td role="cell" class="test ">'. substr($obj['question'], 0, 60).'</td>
                                <td role="cell">'.$obj['chapter_name'].'</td>
                                <td role="cell" class="correctText">'.$status.'</td>
                                <div style = "display:none;" class = "sta">'.$status.'</div>
                                <div style = "display:none;" class = "chap">'.$obj['chapter_name'].'</div>
                                <div style = "display:none;" class = "question">'.$obj['question'].'</div>
                                <div style = "display:none;" class = "ch_a">'.$obj['choice_a'].'</div>
                                <div style = "display:none;" class = "ch_b">'.$obj['choice_b'].'</div>
                                <div style = "display:none;" class = "ch_c">'.$obj['choice_c'].'</div>
                                <div style = "display:none;" class = "ch_d">'.$obj['choice_d'].'</div>
                                <div style = "display:none;" class = "solution">'.$obj['solution'].'</div>
                                <div style = "display:none;" class = "stu-ans">'.$obj['answer'].'</div>
                            </tr>';
                $i++;
        }
        return $output;
        }
        $query = DB::table('homework_logs')
        ->leftjoin('exam_news','homework_logs.exam_id','=','exam_news.id')
        ->leftjoin('chapters','exam_news.chapter_id','chapters.id')
        ->select(DB::raw('homework_logs.exam_id,homework_logs.answer,homework_logs.is_correct,exam_news.question,exam_news.choice_a,exam_news.choice_b,exam_news.choice_c,exam_news.choice_d,exam_news.local_pic,exam_news.answer as solution,chapters.name as chapter_name'))
        ->where('group_hw_id','=',$group)
        ->where('line_code','=',$choosechild)
        ->get();
        $query = json_decode($query, TRUE);
        $output = "";
        $i = '1';
        foreach($query as $obj){
            if($obj['is_correct'] == 1){
                $status = 'ถูก';
            }else{
                $status = 'ผิด';
            }
            $output .= '<tr role="row" id = "'.$i.'" class = "exam-ele"  onclick="pop(this.id)">
                            <td role="cell">'. $i .'</td>
                            <td role="cell" class="test ">'. substr($obj['question'], 0, 60).'</td>
                            <td role="cell">'.$obj['chapter_name'].'</td>
                            <td role="cell" class="correctText">'.$status.'</td>
                            <td style = "display:none;" class = "sta">'.$status.'</div>
                            <td style = "display:none;" class = "chap">'.$obj['chapter_name'].'</div>
                            <td style = "display:none;" class = "question">'.$obj['question'].'</div>
                            <td style = "display:none;" class = "ch_a">'.$obj['choice_a'].'</div>
                            <td style = "display:none;" class = "ch_b">'.$obj['choice_b'].'</div>
                            <td style = "display:none;" class = "ch_c">'.$obj['choice_c'].'</div>
                            <td style = "display:none;" class = "ch_d">'.$obj['choice_d'].'</div>
                            <td style = "display:none;" class = "solution">'.$obj['solution'].'</div>
                            <td style = "display:none;" class = "stu-ans">'.$obj['answer'].'</div>
                        </tr>';
            $i++;
        }
        return $output;
    }
}
