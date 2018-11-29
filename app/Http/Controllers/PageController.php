<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class Pagecontroller extends Controller
{
    public function getLaravelpage(){
        return view('welcome');
    }
    public function addchildpage(){
        return view('addchild');
    }
    public function getchoosepage(){
      //require picture,name
      if(session()->has('username')){
      return view('choose');
      }else{
        return redirect('/');
      }
    }
    public function getsteppage(){
      if(session()->has('username')){
      $childdata = session('childdata','default');
      return view('step');
      }else{
        return redirect('/');
      }
    }
    public function getuserpage(){
      if(session()->has('username') || session()->has('choosechild')){
        $jsonsubject = DB::table('subjects')->get();
        $jsonchapters = DB::table('chapters')->get();
        $arraysubject = json_decode($jsonsubject, true);
        $arraychapters = json_decode($jsonchapters, true);
        Session::put('subject_list',$arraysubject);
        Session::put('chapter_list',$arraychapters);
        // return $arraychapters;
        // $line_code = 'st11';
        return view('overallAndsubject');
      }else{
        return redirect('/');
      }

    }
    public function dashboard(){
      if(session()->has('username')){
        if(session()->has('classstatus')){
          $id = session('id', 'default');
          $classid = session('classstatus', 'default');
          $queryclasscode = DB::table('classrooms')->select('classroom_code','name')->where('id',$classid)->get();
          $codeclass = json_decode($queryclasscode,true);
          Session::put('classcode',$codeclass);
          // query child
          $jsonresult = DB::table('students')
          ->leftjoin('info_classrooms','students.line_code','=','info_classrooms.line_code')
          ->select(DB::raw('students.line_code,students.line_code,students.local_pic,students.name,info_classrooms.id'))
          ->where('info_classrooms.classroom_id',$classid)
          ->orderBy('students.name','asc')
          ->get();
          $arrayresult = json_decode($jsonresult, true);
          Session::put('childdata',$arrayresult);
          // return $arrayresult; 
          $student_mean_sub1 = DB::table('groups')
          ->leftjoin('info_classrooms','groups.line_code','=','info_classrooms.line_code')
          ->leftjoin('chapters', 'groups.chapter_id', '=', 'chapters.id')
          ->leftjoin('subjects', 'chapters.subject_id', '=', 'subjects.id')
          ->leftjoin('studentparents','groups.line_code','=','studentparents.line_code')
          ->rightjoin('students','groups.line_code','=','students.line_code')
          ->select(DB::raw('groups.line_code,students.name,subjects.name as subject_name, sum(score) / count(score) as mean'))
          ->where('info_classrooms.classroom_id',$classid)
          ->where('chapters.subject_id', '=', '1' )
          // ->where('status', '=', '1' )
          ->groupBy('groups.line_code','students.name')
          ->get();
          // return $student_mean_sub1;
          $arrayresult = json_decode($student_mean_sub1, true);
          Session::put('sub1',$arrayresult);
          Session::put('sub1_json',$student_mean_sub1);

          $student_mean_sub2 = DB::table('groups')
          ->select(DB::raw('groups.line_code,students.name,subjects.name as subject_name, sum(score) / count(score) as mean'))
          ->leftjoin('info_classrooms','groups.line_code','=','info_classrooms.line_code')
          ->leftjoin('chapters', 'groups.chapter_id', '=', 'chapters.id')
          ->leftjoin('subjects', 'chapters.subject_id', '=', 'subjects.id')
          ->leftjoin('studentparents','groups.line_code','=','studentparents.line_code')
          ->rightjoin('students','groups.line_code','=','students.line_code')
          ->where('info_classrooms.classroom_id',$classid)
          ->where('chapters.subject_id', '=', '2' )
          // ->where('status', '=', '1' )
          ->groupBy('groups.line_code','students.name')
          ->get();
          // return $student_mean_sub2;
          $arrayresult = json_decode($student_mean_sub2, true);
          Session::put('sub2',$arrayresult);
          Session::put('sub2_json',$student_mean_sub2);

          $meanoverall = DB::table(DB::raw("(select groups.line_code,subjects.name,sum(score) / count(score) as score from groups
                                            left join info_classrooms on groups.line_code = info_classrooms.line_code
                                            left join chapters on chapter_id = chapters.id
                                            left join subjects on subject_id = subjects.id
                                            left join studentparents on groups.line_code = studentparents.line_code
                                            where subject_id = 1 and info_classrooms.classroom_id = $classid
                                            group by groups.line_code
                                            order by groups.id) total"))
          ->select(DB::raw('sum(score) / count(score) as mean'))
          ->get();
          $arrayresult = json_decode($meanoverall, true);
          Session::put('meansub1',$arrayresult);
          $meanoverall2 = DB::table(DB::raw("(select groups.line_code,subjects.name,sum(score) / count(score) as score from groups
                                            left join info_classrooms on groups.line_code = info_classrooms.line_code
                                            left join chapters on chapter_id = chapters.id
                                            left join subjects on subject_id = subjects.id
                                            left join studentparents on groups.line_code = studentparents.line_code
                                            where subject_id = 2 and info_classrooms.classroom_id = $classid
                                            group by groups.line_code
                                            order by groups.id) total"))
          ->select(DB::raw('sum(score) / count(score) as mean'))
          ->get();
          $arrayresult = json_decode($meanoverall2, true);
          Session::put('meansub2',$arrayresult);
          
          return view('dashboard');
        }else{
          return redirect('/selectclass');
        }
      }else{
        return redirect('/');
      }
    }
    public function gethome(){
        if(session()->has('username')){
          if(session()->has('classstatus')){
            return redirect('/dashboard');
          }else{
            return redirect('/selectclass');
          }
        }else{
            return view('home');
        }
    }
    public function addchild($id){
        Session::put('line_code',$id);
        $queryresult = DB::table('students')
        ->select(DB::raw('local_pic'))
        ->where('line_code' , $id)
        ->get();
        $arrayresult = json_decode($queryresult, true);
        if (count($arrayresult)) {
          $result = $arrayresult[0]["local_pic"];
          Session::put('local_pic',$result);
          return redirect('/addchild')->with('code',$id);
        }else{
          return redirect('/error')->with('reporttype','Error')->with('reportdetail','Linecode not found');
        }
    }
    public function studentinfo(){
      return view('studentinfo');
    }
    public function connectpage(){
      return view('connectstu');
    }
    public function selectclass(){
      $id = session('id', 'default');
      $query = DB::table('classrooms')->select('name','id')->where('parent_id','=',$id)->get();
      $listclass = json_decode($query, true);
      Session::put('listclass',$listclass);
      return view('select_class');
    }
    public function newclass(){
      if(session()->has('id')){
        return view('newclass');
      }else{
        return redirect('/');
      }
    }
    public function newgroupexam(){
      if(session()->has('id')){
        return view('newgroupexam');
      }else{
        return redirect('/');
      }
    }
    public function chooesclassroom($id){
      Session::put('classstatus',$id);
      return redirect('dashboard');
    }
    public function status(){
      return view('status');
    }
    public function error(){
      return view('status');
    }
    public function aboutexam(){
      if(session()->has('id')){
        $jsonsubject = DB::table('subjects')->get();
        $jsonchapter = DB::table('chapters')->get();
        $parent_id = session('id','default');
        // $queryresult = DB::table('exam_news')
        //   ->select(DB::raw('level_id,question,choice_a,choice_b,choice_c,choice_d,local_pic,exam_news.answer,principle_id'))
        //   ->leftjoin('logChildrenQuizzes','exam_news.id','=','logChildrenQuizzes.exam_id')
        //   ->groupBy('exam_news.id')
        //   ->orderby('exam_news.id','asc')
        //   ->get();
        $queryresult = DB::table("exam_news")
          ->select('id','question','choice_a','choice_b','choice_c','choice_d','local_pic','answer','updated_at')
          ->orderBy('id','asc')
          ->get();
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
        // return $jsoncorrect;
        $jsongroup = DB::table('examgroups')
        ->leftjoin('send_groups','examgroups.id','=','send_groups.examgroup_id')
        ->select('examgroups.id','examgroups.parent_id','examgroups.name')
        ->where('parent_id','=',$parent_id)
        ->whereNull('send_groups.examgroup_id')
        ->get();
        $readgroup = DB::table('examgroups')
        ->select('examgroups.id','examgroups.parent_id','examgroups.name')
        ->where('parent_id','=',$parent_id)
        ->get();
        // dd($readgroup);
        $listgroup = DB::table('classrooms')
        ->select('classrooms.id','classrooms.name')
        ->where('classrooms.parent_id','=',$parent_id)
        ->get();
        // dd($listgroup);
        return view('exam')
        ->with('readgroup',$readgroup)
        ->with('listgroup',$listgroup)
        ->with('jsonsubject',$jsonsubject)
        ->with('jsonchapter',$jsonchapter)
        ->with('jsoncorrect',$jsoncorrect)
        ->with('jsonwrong',$jsonwrong)
        ->with('jsongroup',$jsongroup)
        ->with('queryresult',$queryresult);
      }else{
        return view('home');
      }
    }
    public function loginadminpage(){
      return view('login_ad');
      if(session()->has('admin_id')){
        return redirect('/adminpage');
      }else{
        return view('login_ad');
      }
    }
    public function adminpage(){

    }
    public function detailN(){
      return view('detailN');
    }
    public function detailchapter(){
      return view('detailChapter');
    }
    public function addFeedback(Request $request){
      $user_id = session('id', 'default');
      $type_id = $request->input('type_id');
      $head = $request->input('head');
      $detail = $request->input('detail');
      // dd($type_id, $head, $detail);

      DB::table('feedbacks')->insert([
        'typereport_id' => $type_id,
        'head' => $head,
        'details' => $detail,
        'parent_id' => $user_id
      ]);
      return back()->withErrors(['ส่งการรายงานปัญหาเรียบร้อย']);
    }

}
