<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Session;

class detail_new_Controller extends Controller
{
    public function calldetailN($id){
        $choosechild = $id;
        $jsonchapters = DB::table('chapters')->get();
        $jsonchooeschilddata = DB::table('students')->where('line_code','=',$choosechild)->get();
        $jsonmeanscore = DB::table(
            DB::raw("(select chapter_id as id,count(score) as count from groups
            where score IS not null and line_code = '$choosechild'
            group by chapter_id) temp"))
            ->leftjoin('groups','temp.id','=','groups.chapter_id')
            ->leftjoin('chapters','temp.id','=','chapters.id')
            ->select(DB::raw('sum(score)/temp.count as mean,chapters.name,temp.id'))
            ->where('line_code','=',$choosechild)
            ->groupby('groups.chapter_id')
            ->get();
        $homeworklist = DB::table('info_classrooms')
        ->select(DB::raw('send_groups.examgroup_id,examgroups.name'))
        ->rightjoin('send_groups','send_groups.room_id','=','info_classrooms.classroom_id')
        ->leftjoin('examgroups','examgroups.id','=','send_groups.examgroup_id')
        ->where('info_classrooms.line_code','=', $choosechild)
        ->get();
        // DD($homeworklist);
        // dd($jsonchapters);
        Session::put('homeworklist',$homeworklist);
        Session::put('chapters',$jsonchapters);
        Session::put('chooeschilddata',$jsonchooeschilddata);
        Session::put('meanscore',$jsonmeanscore);
        Session::put('choosechild',$choosechild);
        return redirect('/detailN');
    }
    public function calldetailchapter($id){
        $choosechild = session('choosechild','default');
        $chapter = $id;
        $chaptername= DB::table('chapters')->select('name')->where('id','=',$id)->get();
        $jsonchooeschilddata = DB::table('students')->where('line_code','=',$choosechild)->get();

        $sumoverall = DB::table('results')
        ->select(DB::raw('sum(total_level) as max,sum(total_level_true) as `true`'))
        ->leftjoin('groups','results.group_id','=','groups.id')
        ->where('groups.line_code','=',$choosechild)
        ->get();
        $jsonchapters = DB::table('chapters')->where('subject_id','=','1')->get();
        // $log = DB::table('results')
        // ->leftjoin('groups','results.group_id','=','groups.id')
        // ->where('groups.line_code','=',$choosechild)
        // ->get();
        $extenlevel = DB::table(DB::raw("(select chapter_id,id as group_id from groups where status = 1 and chapter_id = '$chapter' and line_code = '$choosechild' order by id desc limit 1) lastgroup"))
        ->join('groups','lastgroup.group_id','=','groups.id')
        ->leftjoin('results','groups.id','=','results.group_id')
        ->leftjoin('chapters','groups.chapter_id','=','chapters.id')
        ->select('results.level_id','results.total_level','results.total_level_true')
        ->get();
        // Exten level easy normal hard

        $student_score_chapter = DB::table('results')
        ->select(DB::raw('chapters.name as chapter_name,sum(level_id * total_level_true) as score'))
        ->leftjoin('groups','results.group_id', '=','groups.id')
        ->leftjoin('chapters','chapter_id', '=', 'chapters.id')
        ->where('chapter_id', '=', $chapter)
        ->where('groups.line_code', '=', $choosechild)
        ->groupBy('group_id')
        ->get(); //คะแนนเด็ก
        $overall_score = DB::table('results')
        ->select(DB::raw('sum(level_id * total_level_true) as score'))
        ->leftjoin('groups','results.group_id', '=','groups.id')
        ->leftjoin('chapters', 'groups.chapter_id', '=', 'chapters.id')
        ->leftjoin('subjects', 'chapters.subject_id', '=', 'subjects.id')
        ->where('chapters.id', '=', $chapter)
        ->get(); //คะแนนบาร์ชาตรวม
        $student_count = DB::table('results')
        ->select(DB::raw('count(distinct groups.line_code) as count'))
        ->leftjoin('groups', 'groups.id', '=', 'results.group_id')
        ->leftjoin('chapters', 'groups.chapter_id', '=', 'chapters.id')
        ->leftjoin('subjects', 'chapters.subject_id', '=', 'subjects.id')
        ->where('chapters.subject_id', '=', 1)
        ->where('chapters.id', '=', 1)
        ->groupBy('chapter_id')
        ->get(); //นับจะนวนเด็กที่ทำ

        
       
        Session::put('jsonchooeschilddata',$jsonchooeschilddata);
        Session::put('chaptername',$chaptername);
        Session::put('jsonchapters',$jsonchapters);
        Session::put('student_score_chapter',$student_score_chapter);
        Session::put('overall_score',$overall_score);
        Session::put('student_count',$student_count);
        Session::put('extenlevel',$extenlevel);
        return redirect('/detailchapter');
    }
    public function change_chapter(Request $request){
        $chapter = $request->input('chapter');
        $url = "/detailchapter/".$chapter;
        // dd($url);
        return redirect($url);
    }
}
