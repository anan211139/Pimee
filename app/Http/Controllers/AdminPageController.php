<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Manager;

use Session;

class AdminPageController extends Controller
{
    //
    public function index(){
      return view('mail_ad');
    }
    public function sort(Request $request){
      echo $request->get('value');
      $sort_id = $request->get('value');

      // dd($request);
      if($sort_id == 0){
        $mail_info = DB::table('feedbacks')
        ->select('id', 'head', 'details', 'created_at')
        ->get();
      } else{
        $mail_info = DB::table('feedbacks')
        ->select('id', 'head', 'details', 'created_at')
        ->where('typereport_id', $sort_id)
        ->get();
      }

      $count = count($mail_info);
      if ($count > 0) {
        $location = '/mail/readmail/';
        $mail_sort = '';
        foreach($mail_info as $info){
          $mail_sort .= '
          <tr role="row" class = "mail"  onclick="window.location.href = '."'".$location.$info->id."'".'">
              <td role="cell" onclick="event.stopPropagation();"><input  type="checkbox" name="delete_checkbox[]" class="delete_checkbox" value = "'.$info->id.'"></td>
              <td role="cell">'.$info->id.'</td>
              <td role="cell">'.$info->head.'</td>
              <td role="cell">'.$info->details.'</td>
              <td role="cell">'.$info->created_at.'</td>
          </tr>';
        }

        echo $mail_sort;
      } else {
        $mail_sort = '
        <tr role="row" class = "mail" >
            <td role="cell" colspan="5">ยังไม่มีข้อมูลในขณะนี้</td>
        </tr>';
        echo $mail_sort;
      }


    }

    public function readMail($id){
      $readMail_info = DB::table('feedbacks')
      ->select('feedbacks.id as id', 'head', 'details', 'name')
      ->leftjoin('managers', 'managers.id', '=', 'feedbacks.parent_id')
      ->where('feedbacks.id', $id)
      ->get();

      $type_id = DB::table('feedbacks')
      ->select('typereport_id')
      ->where('id', $id)
      ->get();

      $typeid = json_decode($type_id, true);
      $header = "";
      $span = "";
      // dd($id);
      if ($typeid[0]['typereport_id'] == 1) {
        $header = "แบบฝึกหัด ID :";
        $exam_id = DB::table('feedbacks')
        ->select('exam_id')
        ->where('id', $id)
        ->get();
        $exam_id = json_decode($exam_id, true);
        $span = $exam_id[0]['exam_id'];
      } elseif ($typeid[0]['typereport_id'] == 2) {
        $header = "เกี่ยวกับแชทบอท";
      } elseif ($typeid[0]['typereport_id'] == 3) {
        $header = "เกี่ยวกับเว็บไซต์";
      }

      return view('read_mail')
      ->with('readMail_info',$readMail_info)
      ->with('header', $header)
      ->with('span', $span)
      ->with('id', $id);
    }

    public function addExam(){
      $subject = DB::table('subjects')
      ->get();
      return view('add_exam')
      ->with('subject', $subject);
    }

    public function subject(Request $request){
      echo $request->get('value');
      $subject_id = $request->get('value');
      $chapter = DB::table('chapters')
      ->select('chapters.id as id', 'chapters.name as name')
      ->leftjoin('subjects', 'subjects.id', '=', 'chapters.subject_id')
      ->where('subjects.id', $subject_id)
      ->get();

      $select_chapter = '<option value="">เลือกบท</option>';
      foreach ($chapter as $chapter_name) {
        $select_chapter .=
        '<option value="'.$chapter_name->id.'">'.$chapter_name->name.'</option>';
      }
      echo $select_chapter;
    }

    public function addExamSubmit(Request $request){
      $user_id = session('id', 'default');
      $chapter_id = $request->input('chapter');
      $level = $request->input('level');
      $question = $request->input('exam');
      $choice_a = $request->input('choice1');
      $choice_b = $request->input('choice2');
      $choice_c = $request->input('choice3');
      $choice_d = $request->input('choice4');
      $answer = $request->input('answer');

      $img = $request->file('image');
      $input['imagename'] = time().'.'.$img->getClientOriginalExtension();
      $destinationpath = public_path('/img/exam');
      $img->move($destinationpath, $input['imagename']);

      $path = 'img/exam/'.$input['imagename'];

      DB::table('exam_news')->insert(
        ['chapter_id' => $chapter_id,
        'level_id' => $level,
        'question' => $question,
        'choice_a' => $choice_a,
        'choice_b' => $choice_b,
        'choice_c' => $choice_c,
        'choice_d' => $choice_d,
        'answer' => $answer,
        'parent_id' => $user_id,
        'local_pic' => $path
          ]
      );
      return redirect('/addExam_admin');
    }

    public function deleteFeedback(Request $request){
      $feedback_id_array = $request->input('feedback_id');
      DB::table('feedbacks')->whereIn('id', $feedback_id_array)->delete();
      echo "ลบการรายงานปัญหาเรียบร้อย";
    }

    public function deleteEachFeedback(Request $request){
      $feedback_id = $request->input('feedback_id');
      DB::table('feedbacks')->where('id', $feedback_id)
      ->delete();
        echo "ลบการรายงานปัญหาเรียบร้อย";
    }


}
